<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Services\AuthorizeNetService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Mail\InvoiceIssued;
use App\Mail\InvoiceAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class UpgradeController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $currentPlanId = $user?->currentPlan()?->id;

        $plans = Plan::query()
            ->where('is_active', true)
            ->where('visibility', 'Public')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get([
                'id', 'name', 'slug', 'price', 'image_limit', 'description',
                'verifications_included', 'features', 'cta_label', 'cta_route', 'anet_plan_id'
            ]);

        // Default payment method like Billing page
        $defaultCard = $user?->cards()->where('is_default', true)->first() ?? $user?->cards()->latest()->first();
        $paymentMethod = $defaultCard ? [
            'brand' => $defaultCard->brand,
            'last4' => $defaultCard->last4,
            'exp' => str_pad((string) $defaultCard->exp_month, 2, '0', STR_PAD_LEFT) . '/' . substr((string) $defaultCard->exp_year, -2),
        ] : null;

        return Inertia::render('app/Upgrade', [
            'plans' => $plans,
            'currentPlanId' => $currentPlanId,
            'paymentMethod' => $paymentMethod,
        ]);
    }

    public function switch(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($data['plan_id']);

        $current = $user?->activeSubscription();
        if ($current && $current->plan_id === $plan->id) {
            return back()->with('info', 'You are already on the '.$plan->name.' plan.');
        }

        DB::transaction(function () use ($user, $plan, $current) {
            // 1) Cancel current subscription at provider and mark locally
            if ($current) {
                try {
                    // If we have a provider subscription id (Authorize.Net ARB), attempt cancellation
                    if (!empty($current->provider_subscription_id) || !empty($current->anet_subscription_id)) {
                        // Backward compat: some code may have anet_subscription_id column
                        $arbId = $current->provider_subscription_id ?? $current->anet_subscription_id;
                        if ($arbId) {
                            app(AuthorizeNetService::class)->cancelARB((string) $arbId);
                        }
                    }
                } catch (\Throwable $e) {
                    // Swallow provider cancel errors to avoid blocking upgrades; could be logged
                }

                // Mark local subscription canceled immediately
                $current->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                    'cancel_at_period_end' => false,
                    'renews_at' => null,
                ]);

                // Event
                SubscriptionEvent::query()->create([
                    'subscription_id' => $current->id,
                    'actor_user_id' => $user->id,
                    'event' => 'canceled',
                    'old_values' => ['plan_id' => $current->plan_id],
                    'new_values' => null,
                    'metadata' => ['reason' => 'upgrade'],
                    'created_at' => now(),
                ]);
            }

            // 2) Create new ARB subscription at provider (if user has profiles)
            $providerSubId = null;
            if (!empty($user->anet_customer_profile_id) && !empty($user->anet_customer_payment_profile_id)) {
                $anet = app(AuthorizeNetService::class);
                $amountCents = (int) round((float) ($plan->price ?? 0) * 100);
                $arb = $anet->createARBSubscription(
                    $user->anet_customer_profile_id,
                    $user->anet_customer_payment_profile_id,
                    $plan->name,
                    $amountCents,
                    'months',
                    1,
                );
                $providerSubId = $arb['subscriptionId'] ?? null;
            }

            // 3) Create new local subscription record
            $new = $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->copy()->addMonth(),
                'renews_at' => now()->copy()->addMonth(),
                'currency' => 'USD',
                'price_monthly_cents' => (int) round((float) ($plan->price ?? 0) * 100),
                'included_verifications' => (int) ($plan->verifications_included ?? 0),
                'overage_price_per_unit_cents' => (int) ($plan->overage_price_per_unit_cents ?? 0),
                'provider' => $providerSubId ? 'authorize_net' : 'local',
                'provider_customer_id' => $user->anet_customer_profile_id ?? null,
                'provider_subscription_id' => $providerSubId,
                'metadata' => ['source' => 'upgrade'],
            ]);

            SubscriptionEvent::query()->create([
                'subscription_id' => $new->id,
                'actor_user_id' => $user->id,
                'event' => 'started',
                'old_values' => null,
                'new_values' => ['plan_id' => $plan->id],
                'metadata' => ['source' => 'upgrade'],
                'created_at' => now(),
            ]);

            // Create invoice for the upgrade (new period)
            $priceMonthlyCents = (int) round((float) ($plan->price ?? 0) * 100);
            $invoice = Invoice::create([
                'subscription_id' => $new->id,
                'user_id' => $user->id,
                'number' => Invoice::generateInvoiceNumber(),
                'status' => 'open',
                'currency' => 'USD',
                'subtotal_cents' => $priceMonthlyCents,
                'discount_cents' => 0,
                'tax_cents' => 0,
                'total_cents' => $priceMonthlyCents,
                'period_start' => $new->current_period_start,
                'period_end' => $new->current_period_end,
                'provider' => $providerSubId ? 'authorize_net' : 'local',
                'provider_invoice_id' => null,
                'metadata' => ['source' => 'upgrade'],
                'issued_at' => now(),
                'due_at' => now(),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'type' => 'base_fee',
                'description' => 'Subscription - ' . ($plan->name ?? 'Plan') . ' (Monthly)',
                'quantity' => 1,
                'unit_price_cents' => $priceMonthlyCents,
                'amount_cents' => $priceMonthlyCents,
                'metadata' => ['plan_id' => $plan->id, 'plan_slug' => $plan->slug],
            ]);

            // Send emails: user and admin
            try {
                Mail::to($user->email)->send(new InvoiceIssued($invoice));
                $adminEmail = config('mail.admin.address')
                    ?? env('ADMIN_EMAIL')
                    ?? config('mail.from.address');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new InvoiceAdminNotification($invoice));
                }
            } catch (\Throwable $e) {
                // Swallow email errors; consider logging
            }
        });

        return redirect()->route('app.upgrade')->with('success', 'Plan updated to '.$plan->name.'.');
    }
}
