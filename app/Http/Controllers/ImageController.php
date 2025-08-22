<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'], // 5MB
        ]);

        $user = $request->user();
        if (! $user || ! $user->subscribed('default')) {
            return back()->withErrors(['image' => 'You need an active subscription to upload images.']);
        }

        $plan = $user->currentPlan();
        if (! $plan) {
            return back()->withErrors(['image' => 'Unable to resolve your subscription plan.']);
        }

        $cycleStart = optional($user->subscription('default'))->created_at;
        $uploadedThisCycle = $user->images()
            ->when($cycleStart, fn($q) => $q->where('created_at', '>=', $cycleStart))
            ->count();

        if ($uploadedThisCycle >= $plan->image_limit) {
            return back()->withErrors(['image' => 'You have reached your image upload limit for this billing cycle.']);
        }

        $path = $request->file('image')->store('uploads/images', 'public');

        Image::create([
            'user_id' => $user->id,
            'path' => $path,
        ]);

        return back()->with('success', 'Image uploaded successfully.');
    }
}


