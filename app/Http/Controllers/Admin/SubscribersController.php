<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscribersController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::query()
            ->with(['subscriptions' => function ($q) {
                $q->orderByDesc('current_period_end');
            }, 'subscriptions.plan'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search');
                $q->where(function ($w) use ($s) {
                    $w->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%");
                });
            })
            ->orderBy('id', 'desc');

        $users = $query->paginate(20)->through(function (User $u) {
            $sub = $u->subscriptions->first();
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'status' => $sub?->status,
                'plan' => $sub?->plan?->name,
                'periodEnd' => optional($sub?->current_period_end)?->toDateString(),
            ];
        });

        $metrics = [
            'active' => Subscription::where('status', 'active')->count(),
            'trialing' => Subscription::where('status', 'trialing')->count(),
            'past_due' => Subscription::where('status', 'past_due')->count(),
            'canceled' => Subscription::where('status', 'canceled')->count(),
        ];

        return Inertia::render('Admin/Subscribers/Index', [
            'filters' => [
                'search' => $request->string('search'),
            ],
            'items' => $users,
            'metrics' => $metrics,
        ]);
    }

    public function show(User $user): Response
    {
        $user->load(['subscriptions.plan', 'subscriptions.invoices.items', 'subscriptions.invoices.payments', 'subscriptions.usages']);

        $subscription = $user->subscriptions->first();

        return Inertia::render('Admin/Subscribers/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'plan' => $subscription->plan?->name,
                'period' => [
                    'start' => optional($subscription->current_period_start)?->toDateString(),
                    'end' => optional($subscription->current_period_end)?->toDateString(),
                ],
                'included' => $subscription->included_verifications,
                'used' => optional($subscription->usages->firstWhere('period_end', $subscription->current_period_end))->used ?? 0,
                'priceMonthly' => (float) ($subscription->price_monthly_cents / 100),
            ] : null,
            'invoices' => $subscription?->invoices->map(function ($inv) {
                return [
                    'number' => $inv->number,
                    'status' => $inv->status,
                    'total' => (float) ($inv->total_cents / 100),
                    'issued_at' => optional($inv->issued_at)?->toDateString(),
                    'items' => $inv->items->map(function ($it) {
                        return [
                            'type' => $it->type,
                            'description' => $it->description,
                            'qty' => $it->quantity,
                            'unit' => (float) ($it->unit_price_cents / 100),
                            'amount' => (float) ($it->amount_cents / 100),
                        ];
                    }),
                    'payments' => $inv->payments->map(function ($p) {
                        return [
                            'status' => $p->status,
                            'amount' => (float) ($p->amount_cents / 100),
                            'paid_at' => optional($p->paid_at)?->toDateTimeString(),
                            'provider' => $p->provider,
                        ];
                    }),
                ];
            }),
        ]);
    }
}
