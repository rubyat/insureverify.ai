<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    /**
     * Show the subscription checkout page.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        $planId = $request->query('plan');
        $plan = Plan::find($planId);
        if (! $plan) {
            return redirect()->route('plans.index');
        }

        return Inertia::render('Subscription', [
            'plan' => $plan,
            'anet' => [
                'loginId' => config('services.authorizenet.api_login_id'),
                'clientKey' => config('services.authorizenet.client_key'),
                'env' => config('services.authorizenet.environment'),
            ],
        ]);
    }

    /**
     * Create the subscription after payment confirmation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            // Accept.js opaque data
            'opaque_descriptor' => ['required', 'string'],
            'opaque_value' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $user = $request->user();

        // Create/ensure customer and ARB subscription in Authorize.Net
        $anet = app(\App\Services\AuthorizeNetService::class);

        if (! $user->anet_customer_profile_id) {
            $profile = $anet->ensureCustomerProfile($user->email, 'Customer #'.$user->id);
            $user->anet_customer_profile_id = $profile['customerProfileId'];
            $user->save();
        }

        $paymentProfile = $anet->addPaymentProfileOpaque(
            $user->anet_customer_profile_id,
            $validated['opaque_descriptor'],
            $validated['opaque_value'],
            $validated['first_name'],
            $validated['last_name'],
        );

        $user->anet_customer_payment_profile_id = $paymentProfile['customerPaymentProfileId'] ?? null;
        $user->save();

        $amountCents = (int) round($plan->price * 100);
        $arb = $anet->createARBSubscription(
            $user->anet_customer_profile_id,
            $user->anet_customer_payment_profile_id,
            $plan->name,
            $amountCents,
            'months',
            1,
        );

        // Record local subscription
        $user->subscriptions()->create([
            'type' => 'default',
            'plan_id' => $plan->id,
            'anet_profile_id' => $user->anet_customer_profile_id,
            'anet_payment_profile_id' => $user->anet_customer_payment_profile_id,
            'anet_subscription_id' => $arb['subscriptionId'] ?? null,
            'anet_status' => 'active',
            'renews_at' => now()->addMonth(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Subscription active');
    }
}


