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
}
