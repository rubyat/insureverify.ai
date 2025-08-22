<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AuthorizeNetService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SyncAuthorizeNetRenewals extends Command
{
    protected $signature = 'authorizenet:sync-renewals';
    protected $description = 'Check and sync Authorize.Net subscription renewals/statuses';

    public function handle(AuthorizeNetService $anet): int
    {
        $now = Carbon::now();
        $count = 0;

        $users = User::query()->get();
        foreach ($users as $user) {
            $subscription = $user->subscription('default');
            if (! $subscription || empty($subscription->anet_subscription_id)) {
                continue;
            }
            try {
                $status = $anet->getARBStatus($subscription->anet_subscription_id);
                $subscription->anet_status = $status;
                if ($status === 'active') {
                    $subscription->stripe_status = null;
                    $subscription->ends_at = null;
                    // If due, roll the renews_at forward by 1 month and stamp last_renewed_at
                    if ($subscription->renews_at && $subscription->renews_at->isPast()) {
                        $subscription->last_renewed_at = $now;
                        $subscription->renews_at = $subscription->renews_at->copy()->addMonth();
                    }
                }
                $subscription->save();
                $count++;
            } catch (\Throwable $e) {
                $this->error("Failed for user {$user->id}: {$e->getMessage()}");
            }
        }

        $this->info("Synced {$count} subscriptions at {$now}");
        return self::SUCCESS;
    }
}


