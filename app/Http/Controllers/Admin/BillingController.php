<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function index(Request $request): Response
    {
        $invoices = Invoice::query()
            ->with(['subscription.user'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->integer('user_id')))
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->through(function (Invoice $inv) {
                return [
                    'id' => $inv->id,
                    'number' => $inv->number,
                    'status' => $inv->status,
                    'total' => (float) ($inv->total_cents / 100),
                    'currency' => $inv->currency,
                    'issued_at' => optional($inv->issued_at ?? $inv->created_at)?->toDateString(),
                    'user' => [
                        'id' => $inv->user?->id,
                        'name' => $inv->subscription?->user?->name,
                        'email' => $inv->subscription?->user?->email,
                    ],
                ];
            });

        $metrics = [
            'count' => Invoice::count(),
            'paid' => Invoice::where('status','paid')->count(),
            'open' => Invoice::where('status','open')->count(),
            'void' => Invoice::where('status','void')->count(),
        ];

        return Inertia::render('Admin/Billing/Index', [
            'filters' => [
                'status' => $request->string('status'),
                'user_id' => $request->integer('user_id'),
            ],
            'invoices' => $invoices,
            'metrics' => $metrics,
        ]);
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load(['items', 'payments', 'subscription.user']);
        return Inertia::render('Admin/Billing/Show', [
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'status' => $invoice->status,
                'currency' => $invoice->currency,
                'subtotal' => (float) ($invoice->subtotal_cents / 100),
                'tax' => (float) ($invoice->tax_cents / 100),
                'total' => (float) ($invoice->total_cents / 100),
                'period' => [
                    'start' => optional($invoice->period_start)?->toDateString(),
                    'end' => optional($invoice->period_end)?->toDateString(),
                ],
                'issued_at' => optional($invoice->issued_at)?->toDateString(),
                'items' => $invoice->items->map(fn($it) => [
                    'type' => $it->type,
                    'description' => $it->description,
                    'qty' => $it->quantity,
                    'unit' => (float) ($it->unit_price_cents / 100),
                    'amount' => (float) ($it->amount_cents / 100),
                ]),
                'payments' => $invoice->payments->map(fn($p) => [
                    'status' => $p->status,
                    'amount' => (float) ($p->amount_cents / 100),
                    'paid_at' => optional($p->paid_at)?->toDateTimeString(),
                    'provider' => $p->provider,
                ]),
                'user' => [
                    'id' => $invoice->subscription?->user?->id,
                    'name' => $invoice->subscription?->user?->name,
                    'email' => $invoice->subscription?->user?->email,
                ],
            ],
        ]);
    }
}
