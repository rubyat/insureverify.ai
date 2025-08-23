<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                $subscription = $user->subscription('default');
                $plan = $user->currentPlan();
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->all(),
                    'plan' => $plan?->name,
                    'status' => $subscription?->stripe_status,
                ];
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'canManageAdmins' => Auth::user()?->hasRole('super-admin') === true,
        ]);
    }

    public function makeAdmin(User $user): RedirectResponse
    {
        // Only super-admin can access (also protected by route middleware)
        $user->assignRole('admin');
        return back()->with('success', 'User promoted to admin.');
    }

    public function removeAdmin(User $user): RedirectResponse
    {
        // Prevent removing admin from the last super-admin if desired (optional)
        $user->removeRole('admin');
        return back()->with('success', 'Admin role removed from user.');
    }
}


