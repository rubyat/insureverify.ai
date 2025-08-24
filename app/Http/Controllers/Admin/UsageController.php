<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionUsage;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UsageController extends Controller
{
    public function index(Request $request): Response
    {
        $query = SubscriptionUsage::query()->with(['subscription.user']);

        if ($request->filled('subscription_id')) {
            $query->where('subscription_id', $request->integer('subscription_id'));
        }
        if ($request->filled('user_id')) {
            $query->whereHas('subscription', function ($q) use ($request) {
                $q->where('user_id', $request->integer('user_id'));
            });
        }
        if ($request->filled('metric')) {
            $query->where('metric', $request->string('metric'));
        }

        $usages = $query->orderByDesc('period_end')
            ->paginate(25)
            ->through(function (SubscriptionUsage $u) {
                return [
                    'id' => $u->id,
                    'metric' => $u->metric,
                    'used' => (int) $u->used,
                    'period' => [
                        'start' => optional($u->period_start)?->toDateString(),
                        'end' => optional($u->period_end)?->toDateString(),
                    ],
                    'subscription' => [
                        'id' => $u->subscription?->id,
                        'status' => $u->subscription?->status,
                    ],
                    'user' => [
                        'id' => $u->subscription?->user?->id,
                        'name' => $u->subscription?->user?->name,
                        'email' => $u->subscription?->user?->email,
                    ],
                ];
            });

        $metrics = [
            'rows' => SubscriptionUsage::count(),
        ];

        return Inertia::render('Admin/Usage/Index', [
            'filters' => [
                'subscription_id' => $request->integer('subscription_id'),
                'user_id' => $request->integer('user_id'),
                'metric' => $request->string('metric') ?: 'verifications',
            ],
            'usages' => $usages,
            'metrics' => $metrics,
        ]);
    }
}
