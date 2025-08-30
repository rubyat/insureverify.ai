<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\FileManagerService;

class Seo extends Model
{
    use HasFactory;

    protected $table = 'seo';

    protected $fillable = [
        'object_id', 'object_model', 'seo_title', 'seo_index', 'seo_keyword', 'seo_description', 'seo_image', 'canonical_url', 'meta_json'
    ];

    protected $casts = [
        'seo_index' => 'boolean',
        'meta_json' => 'array',
    ];

    protected $appends = [
        'placeholder',
    ];

    public function object()
    {
        // Polymorphic target using custom columns: object_model (type) and object_id (id)
        return $this->morphTo(null, 'object_model', 'object_id');
    }

    public function getPlaceholderAttribute(): ?string
    {
        $path = $this->seo_image;
        if (!is_string($path) || $path === '') {
            return null;
        }
        /** @var FileManagerService $fm */
        $fm = app(FileManagerService::class);
        return $fm->resize($path, 300, 300);
    }
}
