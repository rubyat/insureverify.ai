<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'user_id','plan_id','status','trial_ends_at','current_period_start','current_period_end','renews_at','canceled_at','cancel_at_period_end','currency','price_monthly_cents','included_verifications','overage_price_per_unit_cents','provider','provider_customer_id','provider_subscription_id','metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'cancel_at_period_end' => 'boolean',
        'current_period_start' => 'datetime',
        'current_period_end'   => 'datetime',
        'renews_at'            => 'datetime',
        'canceled_at'          => 'datetime',
        'trial_ends_at'        => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }
    public function usages(): HasMany { return $this->hasMany(SubscriptionUsage::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function events(): HasMany { return $this->hasMany(SubscriptionEvent::class); }

    public function scopeActive($q) { return $q->where('status','active'); }

    /**
     * Return the current billing period window [start, end].
     */
    public function currentPeriod(): array
    {
        $start = $this->current_period_start;
        $end = $this->current_period_end ?: $this->renews_at;
        return [$start, $end];
    }

    /**
     * Included verification limit for this subscription, with plan fallback.
     */
    public function includedVerificationLimit(): int
    {
        // Prefer subscription column if present, otherwise plan config.
        return (int) ($this->included_verifications
            ?? $this->plan?->verifications_included
            ?? $this->plan?->image_limit
            ?? 0);
    }

    /**
     * Get the usage row for the given metric in the current period (if any).
     */
    public function currentPeriodUsage(string $metric = 'verifications'): ?SubscriptionUsage
    {
        [$start, $end] = $this->currentPeriod();
        if (!$start || !$end) return null;

        return $this->usages()
            ->where('metric', $metric)
            ->where('period_start', $start)
            ->where('period_end', $end)
            ->first();
    }

    /**
     * Total used for the metric in the current period.
     */
    public function usedInCurrentPeriod(string $metric = 'verifications'): int
    {
        return (int) optional($this->currentPeriodUsage($metric))->used ?? 0;
    }

    /**
     * Remaining units for the metric in the current period.
     */
    public function remainingForMetric(string $metric = 'verifications'): int
    {
        $limit = $this->includedVerificationLimit();
        $used = $this->usedInCurrentPeriod($metric);
        return max(0, $limit - $used);
    }

    /**
     * Resolve or create the current period usage row for a metric.
     */
    public function resolveOrCreateCurrentPeriodUsage(string $metric = 'verifications'): SubscriptionUsage
    {
        [$start, $end] = $this->currentPeriod();
        return SubscriptionUsage::firstOrCreate([
            'subscription_id' => $this->id,
            'metric' => $metric,
            'period_start' => $start,
            'period_end' => $end,
        ], [
            'used' => 0,
        ]);
    }

    /**
     * Cycle reset date (end of current period) in Y-m-d.
     */
    public function cycleResetDate(?string $format = 'Y-m-d'): ?string
    {
        [, $end] = $this->currentPeriod();
        return $end ? ($format ? $end->format($format) : (string) $end) : null;
    }
}
