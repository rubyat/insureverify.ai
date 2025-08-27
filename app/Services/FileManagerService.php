<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerService
{
    private FilesystemAdapter $disk;
    private string $base;
    private string $cache;

    public function __construct()
    {
        $diskName = config('filemanager.disk', 'public');
        $this->disk = Storage::disk($diskName);
        $this->base = trim(config('filemanager.base', 'catalog'), '/');
        $this->cache = trim(config('filemanager.cache', 'cache'), '/');
    }

    private function basePath(?string $sub = null): string
    {
        $path = $this->base;
        if ($sub) {
            $path .= '/' . trim($sub, '/');
        }
        return $path;
    }

    private function cachePath(?string $sub = null): string
    {
        $path = $this->cache;
        if ($sub) {
            $path .= '/' . trim($sub, '/');
        }
        return $path;
    }

    public function list(string $directory = '', string $filterName = '', int $page = 1, int $limit = 16): array
    {
        $dir = $this->basePath($directory);
        $dir = trim($dir, '/');

        // Normalize
        if ($dir === '') $dir = $this->base;

        $all = $this->disk->allFiles($dir);
        $allDirs = $this->disk->directories($dir);

        $allowedExt = array_map('strtolower', config('filemanager.allowed_extensions', []));

        $files = [];
        foreach ($all as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) continue;
            if ($filterName && !Str::startsWith(basename($file), $filterName)) continue;
            $files[] = $file;
        }

        $directories = [];
        foreach ($allDirs as $d) {
            $name = basename($d);
            if ($filterName && !Str::startsWith($name, $filterName)) continue;
            $directories[] = $d;
        }

        // Pagination over combined list similar to OC which paginates over mixed paths
        $paths = array_merge(
            array_map(fn($d) => $d . '/', $directories),
            $files
        );

        $total = count($paths);
        $offset = max(0, ($page - 1) * $limit);
        $slice = array_slice($paths, $offset, $limit);

        $outDirs = [];
        $outFiles = [];

        foreach ($slice as $p) {
            if (Str::endsWith($p, '/')) {
                $p = rtrim($p, '/');
                $outDirs[] = [
                    'name' => basename($p),
                    'path' => trim(Str::after($p, $this->basePath()), '/'),
                ];
            } else {
                $outFiles[] = [
                    'name' => basename($p),
                    'path' => trim(Str::after($p, $this->basePath()), '/'),
                    'full' => $p,
                ];
            }
        }

        return [
            'directories' => $outDirs,
            'files' => $outFiles,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
            ],
            'directory' => $directory,
            'filter_name' => $filterName,
        ];
    }

    public function upload(array $uploadedFiles, string $directory = ''): array
    {
        $dir = $this->basePath($directory);
        $this->ensureDir($dir);

        $maxKb = (int) config('filemanager.max_upload_kb', 4096);
        $allowedExt = array_map('strtolower', config('filemanager.allowed_extensions', []));
        $allowedMimes = config('filemanager.allowed_mimes', []);

        $saved = [];
        foreach ($uploadedFiles as $file) {
            if (!$file->isValid()) {
                return ['error' => 'Upload error'];
            }
            if (($file->getSize() / 1024) > $maxKb) {
                return ['error' => 'File too large'];
            }
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedExt, true)) {
                return ['error' => 'Invalid file type'];
            }
            if (!in_array($file->getMimeType(), $allowedMimes, true)) {
                return ['error' => 'Invalid file type'];
            }

            $name = basename($file->getClientOriginalName());
            $name = preg_replace('/[\/\\?%*:|"<>]/', '', $name);
            if (mb_strlen($name) < 4 || mb_strlen($name) > 255) {
                return ['error' => 'Invalid filename length'];
            }

            $path = $this->disk->putFileAs($dir, $file, $name);
            $saved[] = $path;
        }

        return ['success' => 'Uploaded successfully', 'files' => $saved];
    }

    public function createFolder(string $folder, string $directory = ''): array
    {
        $dir = $this->basePath($directory);
        $this->ensureDir($dir);

        $folder = preg_replace('/[\/\\?%*&:|"<>]/', '', basename($folder));
        if (mb_strlen($folder) < 3 || mb_strlen($folder) > 128) {
            return ['error' => 'Invalid folder name'];
        }

        $target = trim($dir . '/' . $folder, '/');
        if ($this->disk->exists($target)) {
            return ['error' => 'Folder already exists'];
        }

        $this->disk->makeDirectory($target);
        return ['success' => 'Directory created'];
    }

    public function delete(array $paths): array
    {
        foreach ($paths as $p) {
            $p = trim($p, '/');
            $full = $this->basePath($p);
            if (!$this->disk->exists($full)) {
                return ['error' => 'Invalid path'];
            }
            if (Str::endsWith($p, '/')) {
                $this->disk->deleteDirectory(rtrim($full, '/'));
            } else {
                // Could be file or directory indicator-less; check
                if ($this->disk->directoryExists($full)) {
                    $this->disk->deleteDirectory($full);
                } else {
                    $this->disk->delete($full);
                }
            }
        }
        return ['success' => 'Deleted'];
    }

    public function resize(string $path, int $width, int $height): ?string
    {
        return $this->thumb($path, $width, $height);
    }

    public function thumb(string $path, int $width, int $height): ?string
    {
        $full = $this->basePath($path);
        if (!$this->disk->exists($full)) return null;

        // Build cache dir outside of base, mirroring subdirectories of the original path
        $subdir = trim(dirname($path), '.');
        $cacheDir = $subdir === '' || $subdir === '.'
            ? $this->cachePath()
            : $this->cachePath($subdir);
        $this->ensureDir($cacheDir);

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $nameNoExt = Str::beforeLast(basename($path), '.' . $ext);
        $thumbRel = $cacheDir . '/' . $nameNoExt . '-' . $width . 'x' . $height . '.' . $ext;

        if (!$this->disk->exists($thumbRel) || $this->disk->lastModified($full) > $this->disk->lastModified($thumbRel)) {
            $this->generateThumb($full, $thumbRel, $width, $height);
        }

        return asset('storage/' .$thumbRel);
    }

    public function raw(string $path): ?string
    {
        $full = $this->basePath($path);
        if (!$this->disk->exists($full)) return null;

        return asset('storage/' .$full);
    }

    private function ensureDir(string $dir): void
    {
        if (!$this->disk->exists($dir)) {
            $this->disk->makeDirectory($dir);
        }
    }

    private function urlFor(string $path): string
    {
        // Assumes public disk or similar where url() works
        return $this->disk->url($path);
    }

    private function generateThumb(string $source, string $dest, int $width, int $height): void
    {
        // Use GD directly to avoid extra dependencies
        $tmpIn = $this->disk->path($source);
        $tmpOut = $this->disk->path($dest);

        [$w, $h, $type] = getimagesize($tmpIn);
        if (!in_array($type, [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_WEBP])) {
            // Fallback to copy
            copy($tmpIn, $tmpOut);
            return;
        }

        switch ($type) {
            case IMAGETYPE_PNG: $image = imagecreatefrompng($tmpIn); break;
            case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($tmpIn); break;
            case IMAGETYPE_GIF: $image = imagecreatefromgif($tmpIn); break;
            case IMAGETYPE_WEBP: $image = imagecreatefromwebp($tmpIn); break;
            default: $image = null; break;
        }
        if (!$image) { copy($tmpIn, $tmpOut); return; }

        $scaleW = $width / $w; $scaleH = $height / $h; $scale = min($scaleW, $scaleH);
        $newW = (int)($w * $scale); $newH = (int)($h * $scale);
        $xpos = (int)(($width - $newW) / 2); $ypos = (int)(($height - $newH) / 2);

        $canvas = imagecreatetruecolor($width, $height);
        if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $bg = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
            imagecolortransparent($canvas, $bg);
        } else {
            $bg = imagecolorallocate($canvas, 255, 255, 255);
        }
        imagefilledrectangle($canvas, 0, 0, $width, $height, $bg);

        imagecopyresampled($canvas, $image, $xpos, $ypos, 0, 0, $newW, $newH, $w, $h);

        $ext = strtolower(pathinfo($dest, PATHINFO_EXTENSION));
        if ($ext === 'jpg' || $ext === 'jpeg') imagejpeg($canvas, $tmpOut, 90);
        elseif ($ext === 'png') imagepng($canvas, $tmpOut);
        elseif ($ext === 'gif') imagegif($canvas, $tmpOut);
        elseif ($ext === 'webp') imagewebp($canvas, $tmpOut, 90);
        else imagejpeg($canvas, $tmpOut, 90);

        imagedestroy($canvas);
        imagedestroy($image);
    }
}
