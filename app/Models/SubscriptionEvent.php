<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionEvent extends Model
{
    public $timestamps = false; // has created_at only

    protected $fillable = [
        'subscription_id','actor_user_id','event','old_values','new_values','metadata','created_at'
    ];

    protected $casts = [
        'old_values'=>'array','new_values'=>'array','metadata'=>'array','created_at'=>'datetime'
    ];

    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_user_id'); }
}
