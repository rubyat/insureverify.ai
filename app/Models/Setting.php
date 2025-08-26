<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'label', 'code', 'key', 'value', 'sort_order', 'type', 'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'sort_order' => 'integer',
    ];

    public function setValueAttribute($val): void
    {
        // If value is array/object -> store as JSON
        if (is_array($val) || is_object($val)) {
            $this->attributes['value'] = json_encode($val);
            return;
        }
        $this->attributes['value'] = $val;
    }

    public function getValueDecodedAttribute()
    {
        $v = $this->attributes['value'] ?? null;
        if ($v === null) return null;
        $decoded = json_decode($v, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $v;
    }
}
