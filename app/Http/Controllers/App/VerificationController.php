<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VerificationController
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $remaining = 0;
        $cycleResetDate = null;
        $atLimit = false;

        if ($user) {
            $subscription = $user->activeSubscription();
            if ($subscription) {
                $remaining = $subscription->remainingForMetric('verifications');
                $cycleResetDate = $subscription->cycleResetDate();
                $limit = $subscription->includedVerificationLimit();
                $atLimit = $limit > 0 ? $remaining <= 0 : false;
            }
        }

        return Inertia::render('app/Verification', [
            'remainingUploads' => $remaining,
            'cycleResetDate' => $cycleResetDate,
            'queue' => [],
            'atLimit' => $atLimit,
            'upgradeUrl' => route('app.upgrade'),
        ]);
    }

    /**
     * Accepts the uploaded file storage path and returns dummy verification info after a delay.
     */
    public function verify(Request $request)
    {
        $data = $request->validate([
            'path' => ['required', 'string'],
        ]);

        $user = $request->user();
        if (! $user) {
            throw ValidationException::withMessages([
                'path' => 'You must be logged in to verify.',
            ]);
        }

        // Load active subscription (mirror ImageController logic)
        $subscription = Subscription::query()
            ->active()
            ->where('user_id', $user->id)
            ->orderByDesc('current_period_start')
            ->first();

        if (! $subscription) {
            throw ValidationException::withMessages([
                'path' => 'No active subscription found.',
            ]);
        }

        // Resolve or create current period usage row for metric 'verifications'
        $usage = $subscription->resolveOrCreateCurrentPeriodUsage('verifications');

        // Enforce included limit
        $included = $subscription->included_verifications ?? $subscription->includedVerificationLimit();
        if (! is_null($included) && $usage->used >= $included) {
            throw ValidationException::withMessages([
                'path' => 'You have reached your verification limit for this billing period.',
            ]);
        }



        // Simulate long-running processing (5 seconds)
        sleep(5);

        // Dummy insurance verification payload
        $payload = [
            'file_path' => $data['path'],
            'verified' => true,
            'policy' => [
                'policy_number' => 'POL-2025-123456',
                'provider' => 'Acme Insurance Co.',
                'insured_name' => 'John Doe',
                'coverage' => 'Comprehensive',
                'effective_date' => now()->subMonths(2)->toDateString(),
                'expiration_date' => now()->addMonths(10)->toDateString(),
                'premium' => 129.99,
                'currency' => 'USD',
                'status' => 'Active',
            ],
            'checks' => [
                ['label' => 'Document OCR', 'status' => 'passed'],
                ['label' => 'Policy Lookup', 'status' => 'passed'],
                ['label' => 'Fraud Signals', 'status' => 'clear'],
                ['label' => 'Name Match', 'status' => 'passed'],
            ],
        ];

        // Persist usage increment + subscription event atomically
        DB::transaction(function () use ($user, $subscription, $usage, $data) {
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
                'metadata' => ['metric' => 'verifications', 'path' => $data['path'], 'source' => 'verify'],
                'created_at' => now(),
            ]);
        });

        return response()->json($payload);
    }
}
