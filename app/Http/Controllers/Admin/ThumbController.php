<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ThumbController extends Controller
{
    public function show(string $size, string $path)
    {
        // $size = "300x300"
        [$w, $h] = array_map('intval', explode('x', strtolower($size)));
        $disk = config('filemanager.disk');        // public
        $fs = Storage::disk($disk);

        // originals live under public disk
        if (!$fs->exists($path)) {
            abort(404);
        }

        $ext = Str::lower(Str::afterLast($path, '.'));
        // cache path: cache/{original-path-without-ext}-{wxh}.{ext}
        $cacheRel = 'cache/' . Str::of($path)->beforeLast('.')->replace(['/', '\\'], '/')
            . '-' . $w . 'x' . $h . '.' . $ext;

        if (!$fs->exists($cacheRel) || $fs->lastModified($path) > $fs->lastModified($cacheRel)) {
            // generate
            $manager = new ImageManager(['driver' => 'gd']); // or 'imagick'
            $image = $manager->read($fs->path($path))
                ->scaleDown($w, $h) // preserves aspect ratio, fits inside box
                ->cover($w, $h, 'center'); // like OC: centered on canvas

            // preserve transparency for png/webp; quality default 90
            $fs->put($cacheRel, (string) $image->encodeByExtension($ext));
        }

        $mime = match ($ext) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        return response($fs->get($cacheRel), 200, ['Content-Type' => $mime]);
    }
}
