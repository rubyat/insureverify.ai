<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $subscription = $user->subscription('default');
        $plan = $user->currentPlan();

        return Inertia::render('Billing', [
            'subscription' => [
                'status' => $subscription?->anet_status ?? $subscription?->stripe_status,
                'plan_name' => $plan?->name,
                'price' => $plan?->price,
            ],
        ]);
    }
}


