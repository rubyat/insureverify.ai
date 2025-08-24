<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\SubscriptionEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {



        $request->validate([
            'image' => ['required', 'image', 'max:5120'], // 5MB
        ]);



        // Optional metadata from client
        $uploadFrom = $request->input('upload_from');

        $user = $request->user();
        if (! $user) {
            throw ValidationException::withMessages([
                'image' => 'You must be logged in to upload images.',
            ]);
        }



        // Load active subscription per custom model (no Cashier)
        $subscription = Subscription::query()
            ->active()
            ->where('user_id', $user->id)
            ->orderByDesc('current_period_start')
            ->first();



        if (! $subscription) {
            throw ValidationException::withMessages([
                'image' => 'No active subscription found.',
            ]);
        }



        // Resolve or create current period usage row for metric 'verifications'
        $periodStart = $subscription->current_period_start;
        $periodEnd = $subscription->current_period_end;
        $usage = SubscriptionUsage::firstOrCreate([
            'subscription_id' => $subscription->id,
            'metric' => 'verifications',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ], [
            'used' => 0,
        ]);



        // Enforce included_verifications (block when reached)
        $included = $subscription->included_verifications; // may be null for unlimited
        if (! is_null($included) && $usage->used >= $included) {
            throw ValidationException::withMessages([
                'image' => 'You have reached your verification limit for this billing period.',
            ]);
        }

        // Store file to disk first (non-transactional)
        $path = $request->file('image')->store('uploads/images', 'public');

        // Persist DB updates atomically: increment usage, log event, create image record
        DB::transaction(function () use ($user, $subscription, $usage, $path, $uploadFrom) {
            $oldUsed = $usage->used;
            $usage->used = $oldUsed + 1;
            $usage->last_incremented_at = now();
            $usage->save();

            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'actor_user_id' => $user->id,
                'event' => 'usage_incremented',
                'old_values' => ['used' => $oldUsed],
                'new_values' => ['used' => $usage->used],
                'metadata' => ['metric' => 'verifications', 'upload_from' => $uploadFrom],
                'created_at' => now(),
            ]);

            Image::create([
                'user_id' => $user->id,
                'path' => $path,
            ]);
        });

        // Flash success and uploaded image URL for client preview
        $uploadedUrl = asset('storage/' . $path);
        return back(status: 303)
            ->with('success', 'Image uploaded successfully.')
            ->with('uploaded_image_url', $uploadedUrl);
    }
}


