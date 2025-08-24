<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentsController extends Controller
{
    public function index(Request $request): Response
    {
        $payments = Payment::query()
            ->with(['invoice.subscription.user'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->through(function (Payment $p) {
                return [
                    'id' => $p->id,
                    'status' => $p->status,
                    'amount' => (float) ($p->amount_cents / 100),
                    'currency' => $p->currency,
                    'paid_at' => optional($p->paid_at)?->toDateTimeString(),
                    'provider' => $p->provider,
                    'invoice' => [
                        'id' => $p->invoice?->id,
                        'number' => $p->invoice?->number,
                        'status' => $p->invoice?->status,
                    ],
                    'user' => [
                        'id' => $p->invoice?->subscription?->user?->id,
                        'name' => $p->invoice?->subscription?->user?->name,
                        'email' => $p->invoice?->subscription?->user?->email,
                    ],
                ];
            });

        $metrics = [
            'count' => Payment::count(),
            'succeeded' => Payment::where('status','succeeded')->count(),
            'failed' => Payment::where('status','failed')->count(),
        ];

        return Inertia::render('Admin/Payments/Index', [
            'filters' => [
                'status' => $request->string('status'),
            ],
            'payments' => $payments,
            'metrics' => $metrics,
        ]);
    }

    public function show(Payment $payment): Response
    {
        $payment->load(['invoice.subscription.user']);
        return Inertia::render('Admin/Payments/Show', [
            'payment' => [
                'id' => $payment->id,
                'status' => $payment->status,
                'amount' => (float) ($payment->amount_cents / 100),
                'currency' => $payment->currency,
                'paid_at' => optional($payment->paid_at)?->toDateTimeString(),
                'provider' => $payment->provider,
                'invoice' => [
                    'id' => $payment->invoice?->id,
                    'number' => $payment->invoice?->number,
                    'status' => $payment->invoice?->status,
                ],
                'user' => [
                    'id' => $payment->invoice?->subscription?->user?->id,
                    'name' => $payment->invoice?->subscription?->user?->name,
                    'email' => $payment->invoice?->subscription?->user?->email,
                ],
            ],
        ]);
    }
}
