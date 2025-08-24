<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Inertia\Inertia;
use Inertia\Response;

class ReportsController extends Controller
{
    public function index(): Response
    {
        // Using custom Subscription model and unified status column
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $subscribedUsers = User::all()->filter(fn(User $u) => $u->subscribed())
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


