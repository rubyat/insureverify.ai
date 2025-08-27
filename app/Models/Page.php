<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug', 'title', 'status', 'short_desc', 'image_id', 'custom_logo', 'header_style', 'show_template', 'content', 'template'
    ];

    protected $casts = [
        'template' => 'array',
        'show_template' => 'boolean',
        'status' => 'integer',
    ];

    public function seo()
    {
        return $this->hasOne(Seo::class);
    }
}
