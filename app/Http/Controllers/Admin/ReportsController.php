<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Subscription;

class ReportsController extends Controller
{
    public function index(): Response
    {
        $activeSubscriptions = Subscription::query()
            ->where(function ($q) {
                $q->where('anet_status', 'active')
                  ->orWhere('stripe_status', 'active');
            })
            ->count();

        $subscribedUsers = User::all()->filter(fn(User $u) => $u->subscribed('default'))
            ->values()
            ->map(fn(User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'plan' => $u->currentPlan()?->name,
            ]);

        $invoices = collect();

        return Inertia::render('Admin/Reports/Index', [
            'metrics' => [
                'activeSubscriptions' => $activeSubscriptions,
            ],
            'invoices' => $invoices,
            'subscribedUsers' => $subscribedUsers,
        ]);
    }
}


