<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\SubscriptionUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionRenewalController extends Controller
{
    /**
     * HTTP endpoint for daily renewal processing. Protect with CRON token.
     */
    public function run(Request $request): JsonResponse
    {
        $token = $request->query('token') ?? $request->header('X-Cron-Token');
        $expected = config('app.cron_token') ?? env('CRON_TOKEN');
        if (!$expected || $token !== $expected) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $now = now();
        $results = [
            'processed' => 0,
            'paid' => 0,
            'past_due' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        // Active or trialing subscriptions that reached period end
        $subs = Subscription::query()
            ->whereIn('status', ['active','trialing'])
            ->where('current_period_end', '<=', $now)
            ->get();

        foreach ($subs as $subscription) {
            try {
                DB::transaction(function () use ($subscription, $now, &$results) {
                    $results['processed']++;

                    $user = $subscription->user;

                    // 1) Calculate usage for the ending period
                    $usage = SubscriptionUsage::query()
                        ->where('subscription_id', $subscription->id)
                        ->where('metric', 'verifications')
                        ->where('period_start', $subscription->current_period_start)
                        ->where('period_end', $subscription->current_period_end)
                        ->first();
                    $used = (int) optional($usage)->used ?? 0;
                    $included = (int) ($subscription->included_verifications ?? 0);

                    // 2) Build amounts
                    $baseFeeCents = (int) $subscription->price_monthly_cents;
                    $overageQty = max($used - $included, 0);
                    $overageUnitCents = (int) ($subscription->overage_price_per_unit_cents ?? 0);
                    $overageCents = $overageQty * $overageUnitCents;
                    $subtotal = $baseFeeCents + $overageCents;
                    $tax = 0; // static for now
                    $total = $subtotal + $tax;

                    // 3) Create invoice (open)
                    $invoice = Invoice::query()->create([
                        'subscription_id' => $subscription->id,
                        'user_id' => $user->id,
                        'number' => Invoice::generateInvoiceNumber(),
                        'status' => 'open',
                        'currency' => $subscription->currency ?? 'USD',
                        'subtotal_cents' => $subtotal,
                        'discount_cents' => 0,
                        'tax_cents' => $tax,
                        'total_cents' => $total,
                        'period_start' => $subscription->current_period_start,
                        'period_end' => $subscription->current_period_end,
                        'provider' => $subscription->provider,
                        'provider_invoice_id' => null,
                        'metadata' => ['note' => 'Cron renewal'],
                        'issued_at' => $now,
                        'due_at' => $now->copy()->addDays(7),
                        'paid_at' => null,
                    ]);

                    // 4) Invoice items
                    InvoiceItem::query()->create([
                        'invoice_id' => $invoice->id,
                        'type' => 'base_fee',
                        'description' => 'Base plan fee',
                        'quantity' => 1,
                        'unit_price_cents' => $baseFeeCents,
                        'amount_cents' => $baseFeeCents,
                        'metadata' => ['subscription_id' => $subscription->id],
                    ]);
                    if ($overageQty > 0) {
                        InvoiceItem::query()->create([
                            'invoice_id' => $invoice->id,
                            'type' => 'overage',
                            'description' => 'Overage verifications',
                            'quantity' => $overageQty,
                            'unit_price_cents' => $overageUnitCents,
                            'amount_cents' => $overageCents,
                            'metadata' => ['metric' => 'verifications'],
                        ]);
                    }

                    // 5) Attempt payment (static success for now)
                    $paymentSucceeded = true; // static behavior
                    Payment::query()->create([
                        'invoice_id' => $invoice->id,
                        'provider' => $subscription->provider ?? 'testpay',
                        'provider_payment_intent_id' => 'pi_demo_'.$invoice->id,
                        'status' => $paymentSucceeded ? 'succeeded' : 'failed',
                        'amount_cents' => $total,
                        'currency' => $subscription->currency ?? 'USD',
                        'error_code' => $paymentSucceeded ? null : 'demo_fail',
                        'error_message' => $paymentSucceeded ? null : 'Static demo payment failure',
                        'paid_at' => $paymentSucceeded ? $now : null,
                    ]);

                    $invoice->update([
                        'status' => $paymentSucceeded ? 'paid' : 'open',
                        'paid_at' => $paymentSucceeded ? $now : null,
                    ]);

                    // 6) Events
                    SubscriptionEvent::query()->create([
                        'subscription_id' => $subscription->id,
                        'actor_user_id' => $user->id,
                        'event' => 'invoice_generated',
                        'old_values' => null,
                        'new_values' => ['invoice_number' => $invoice->number],
                        'metadata' => null,
                        'created_at' => $now,
                    ]);
                    SubscriptionEvent::query()->create([
                        'subscription_id' => $subscription->id,
                        'actor_user_id' => $user->id,
                        'event' => $paymentSucceeded ? 'payment_succeeded' : 'payment_failed',
                        'old_values' => null,
                        'new_values' => ['invoice_number' => $invoice->number],
                        'metadata' => null,
                        'created_at' => $now,
                    ]);

                    // 7) Update subscription status per payment result
                    if ($paymentSucceeded) {
                        $results['paid']++;
                        // Advance period
                        $newStart = $subscription->current_period_end;
                        $newEnd = $newStart->copy()->addMonth();

                        $subscription->update([
                            'status' => 'active',
                            'current_period_start' => $newStart,
                            'current_period_end' => $newEnd,
                            'renews_at' => $newEnd,
                        ]);

                        SubscriptionEvent::query()->create([
                            'subscription_id' => $subscription->id,
                            'actor_user_id' => $user->id,
                            'event' => 'period_renewed',
                            'old_values' => ['period_end' => $newStart->toIso8601String()],
                            'new_values' => ['period_end' => $newEnd->toIso8601String()],
                            'metadata' => null,
                            'created_at' => $now,
                        ]);

                        // Reset usage for new period
                        SubscriptionUsage::query()->updateOrCreate([
                            'subscription_id' => $subscription->id,
                            'metric' => 'verifications',
                            'period_start' => $newStart,
                            'period_end' => $newEnd,
                        ], [
                            'used' => 0,
                            'last_incremented_at' => null,
                        ]);
                    } else {
                        $results['past_due']++;
                        $subscription->update(['status' => 'past_due']);
                    }
                });
            } catch (\Throwable $e) {
                $results['errors'][] = [
                    'subscription_id' => $subscription->id,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json($results);
    }
}
