<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        // keeping old column for now, but adding Authorize.Net plan/reference id
        'stripe_plan_id',
        'anet_plan_id',
        'price',
        'image_limit',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'image_limit' => 'integer',
    ];
}


