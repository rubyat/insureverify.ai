<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionUsage extends Model
{
    protected $fillable = [
        'subscription_id','metric','used','period_start','period_end','last_incremented_at'
    ];

    protected $casts = [
        'last_incremented_at' => 'datetime',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
}
