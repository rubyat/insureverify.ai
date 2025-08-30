<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug', 'title', 'status', 'content', 'template',
        'blog_category_id', 'author', 'publish_date', 'tags', 'image'
    ];

    protected $casts = [
        'template' => 'array',
        'status' => 'integer',
        'publish_date' => 'datetime',
        'tags' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'object', 'object_model', 'object_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (Blog $blog) {
            if (method_exists($blog, 'isForceDeleting') && $blog->isForceDeleting()) {
                $blog->seo()->delete();
            }
        });
    }
}
