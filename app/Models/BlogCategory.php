<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug', 'title', 'status', 'content'
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function seo()
    {
        // Polymorphic one-to-one with custom columns used by Seo
        return $this->morphOne(Seo::class, 'object', 'object_model', 'object_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (BlogCategory $model) {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                $model->seo()->delete();
            }
        });
    }
}
