<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImageController extends Controller
{
    public function store(Request $request): JsonResponse
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



        // Load active subscription per custom model
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
        $usage = $subscription->resolveOrCreateCurrentPeriodUsage('verifications');



        // Enforce included_verifications (block when reached)
        $included = $subscription->included_verifications ?? $subscription->includedVerificationLimit(); // may be null for unlimited
        if (! is_null($included) && $usage->used >= $included) {
            throw ValidationException::withMessages([
                'image' => 'You have reached your verification limit for this billing period.',
            ]);
        }

        // Store file to disk first (non-transactional) under per-user directory
        $dir = 'uploads/images/user-' . $user->id;
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }
        $path = $request->file('image')->store($dir, 'public');

        $image = Image::create([
            'user_id' => $user->id,
            'path' => $path,
        ]);

        // Return JSON payload for SPA flow
        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'path' => $path,
            'url' => asset('storage/' . $path),
            // Back-compat keys
            'main_path' => $path,
            'asset_path' => asset('storage/' . $path),
            'image_id' => $image?->id,
        ]);
    }
}
