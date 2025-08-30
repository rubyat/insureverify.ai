<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'items',
        'locations',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
        'locations' => 'array',
    ];
}
