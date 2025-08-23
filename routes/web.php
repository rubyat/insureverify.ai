<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Marketing and public routes moved to routes/frontend.php

Route::get('dashboard', function () {
    $user = Auth::user();
    $images = [];
    $usage = null;
    if ($user) {
        $images = $user->images()->latest()->get()->map(fn($img) => [
            'id' => $img->id,
            'url' => asset('storage/'.$img->path),
        ]);
        $plan = $user->currentPlan();
        $sub = optional($user->subscription('default'));
        $cycleStart = $sub->last_renewed_at ?? $sub->created_at;
        $count = $user->images()->when($cycleStart, fn($q) => $q->where('created_at', '>=', $cycleStart))->count();
        if ($plan) {
            $usage = [
                'used' => $count,
                'limit' => $plan->image_limit,
            ];
        }
    }
    return Inertia::render('Dashboard', [
        'images' => $images,
        'usage' => $usage,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Frontend (customer) routes
require __DIR__.'/frontend.php';

// Admin and Super Admin routes
require __DIR__.'/admin.php';

// Public plans listing moved to routes/frontend.php

// Subscription and other authenticated customer routes moved to routes/frontend.php
