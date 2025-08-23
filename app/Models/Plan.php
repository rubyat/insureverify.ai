<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'description',
        'verifications_included',
        'features',
        'cta_label',
        'cta_route',
        'sort_order',
        'visibility',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'image_limit' => 'integer',
        'verifications_included' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Generate a unique slug based on the provided base string.
     * Appends -2, -3, ... if the slug already exists.
     */
    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($base) ?: 'plan';
        $slug = $baseSlug;
        $i = 2;

        while (static::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
