<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;

    protected $table = 'seo';

    protected $fillable = [
        'page_id', 'seo_title', 'seo_description', 'seo_image_id', 'canonical_url', 'meta_json'
    ];

    protected $casts = [
        'meta_json' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
