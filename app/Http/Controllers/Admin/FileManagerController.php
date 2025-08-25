<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;

class FileManagerController extends Controller
{
    private string $disk;
    private string $baseDir;
    private array $ext;
    private array $mimes;
    private int $perPage;
    private int $maxBytes;

    public function __construct()
    {
        $this->disk = config('filemanager.disk');
        $this->baseDir = trim(config('filemanager.base_dir'), '/');             // catalog
        $this->ext = config('filemanager.allowed_ext');
        $this->mimes = config('filemanager.allowed_mimes');
        $this->perPage = (int) config('filemanager.per_page', 16);
        $this->maxBytes = (int) config('filemanager.max_mb') * 1024 * 1024;
    }

    // GET /admin/api/filemanager/list?directory=&filter_name=&page=
    public function list(Request $request)
    {
        $directory = $this->safeDirectory($request->query('directory', ''));    // '' means root → catalog/
        $filter = (string) $request->query('filter_name', '');
        $page = max((int) $request->query('page', 1), 1);

        $root = $this->join($directory); // absolute path under disk
        $fs = Storage::disk($this->disk);

        if (!$fs->exists($root) || !$fs->directoryExists($root)) {
            return response()->json(['error' => 'Invalid directory'], 422);
        }

        // read immediate children (not recursive), sort folders then files by name
        $all = collect($fs->directories($root))->map(fn($d) => ['type' => 'dir', 'path' => $d])
            ->merge(collect($fs->files($root))->map(fn($f) => ['type' => 'file', 'path' => $f]))
            ->sortBy([
                fn($x) => $x['type'] === 'file', // dirs first
                fn($x) => Str::of($x['path'])->basename()->lower(),
            ])
            ->values();

        // filter files by extension + prefix
        $dirs = $all->where('type', 'dir')->values();
        $files = $all->where('type', 'file')
            ->filter(function ($x) use ($filter) {
                $name = Str::of($x['path'])->basename();
                $okExt = in_array(Str::lower($name->afterLast('.')), $this->ext, true);
                $okPrefix = $filter === '' ? true : Str::startsWith($name, $filter);
                return $okExt && $okPrefix;
            })
            ->values();

        // total paths (dirs + allowed files) for pagination like OpenCart
        $paths = $dirs->pluck('path')->merge($files->pluck('path'))->values();

        $slice = $paths->slice(($page - 1) * $this->perPage, $this->perPage)->values();

        $sliceDirs = $slice->filter(fn($p) => $fs->directoryExists($p))->values();
        $sliceFiles = $slice->filter(fn($p) => $fs->fileExists($p))->values();

        $thumbW = (int) request('thumb_w', config('filemanager.thumb_w'));
        $thumbH = (int) request('thumb_h', config('filemanager.thumb_h'));

        // map dirs
        $directories = $sliceDirs->map(function ($p) use ($root, $directory) {
            $name = Str::of($p)->basename();
            $rel = $this->relativeToBase($p) . '/';
            return [
                'name' => (string) $name,
                'path' => $rel, // relative path under catalog/
            ];
        })->values();

        // map images
        $images = $sliceFiles->map(function ($p) use ($thumbW, $thumbH) {
            $rel = $this->relativeToBase($p); // e.g. foo/bar.jpg
            $name = Str::of($p)->basename();

            return [
                'name'  => (string) $name,
                'path'  => $rel, // store this in DB: "catalog/$rel"
                'href'  => asset('storage/' . $this->baseDir . '/' . $rel),
                'thumb' => route('thumb.show', [
                    'size' => "{$thumbW}x{$thumbH}",
                    'path' => $this->baseDir . '/' . $rel,
                ]),
            ];
        })->values();

        // parent link: remove last segment of directory
        $parent = '';
        if ($directory !== '') {
            $parent = Str::of($directory)->beforeLast('/')->value();
        }

        return response()->json([
            'directories' => $directories,
            'images' => $images,
            'directory' => $directory,     // current relative dir like "banners"
            'filter_name' => $filter,
            'pagination' => [
                'total' => $paths->count(),
                'per_page' => $this->perPage,
                'current_page' => $page,
                'last_page' => (int) ceil(max($paths->count(), 1) / $this->perPage),
            ],
            'parent' => $parent,           // '' means go root
            'config_file_max_size' => $this->maxBytes, // for client-side check
        ]);
    }

    // POST /admin/api/filemanager/upload?directory=
    public function upload(Request $request)
    {
        $directory = $this->safeDirectory($request->query('directory', ''));
        $root = $this->join($directory);
        $fs = Storage::disk($this->disk);

        if (!$fs->exists($root) || !$fs->directoryExists($root)) {
            return response()->json(['error' => 'Invalid directory'], 422);
        }

        $request->validate([
            'file.*' => [
                'required','file',
                'max:' . (int) ($this->maxBytes / 1024),  // KB
                'mimetypes:' . implode(',', $this->mimes),
            ],
        ]);

        foreach ((array) $request->file('file', []) as $upload) {
            $name = $this->sanitizeFileName($upload->getClientOriginalName());

            // length 4..255 (OpenCart parity)
            if (Str::length($name) < 4 || Str::length($name) > 255) {
                return response()->json(['error' => 'Invalid filename length'], 422);
            }

            $ext = Str::lower(Str::afterLast($name, '.'));
            if (!in_array($ext, $this->ext, true)) {
                return response()->json(['error' => 'Invalid file extension'], 422);
            }

            $fs->putFileAs($root, $upload, $name);
        }

        return response()->json(['success' => 'Uploaded successfully']);
    }

    // POST /admin/api/filemanager/folder?directory=  body: folder=New Folder
    public function folder(Request $request)
    {
        $directory = $this->safeDirectory($request->query('directory', ''));
        $root = $this->join($directory);
        $fs = Storage::disk($this->disk);

        if (!$fs->directoryExists($root)) {
            return response()->json(['error' => 'Invalid directory'], 422);
        }

        $folder = $this->sanitizeFolderName($request->string('folder', '')->toString());

        // length 3..128 (OpenCart parity)
        if (Str::length($folder) < 3 || Str::length($folder) > 128) {
            return response()->json(['error' => 'Invalid folder name'], 422);
        }

        if ($fs->directoryExists($root . '/' . $folder)) {
            return response()->json(['error' => 'Folder already exists'], 409);
        }

        $fs->makeDirectory($root . '/' . $folder);
        // optional: create index.html
        $fs->put($root . '/' . $folder . '/index.html', '');

        return response()->json(['success' => 'Folder created']);
    }

    // POST /admin/api/filemanager/delete  body: path[]=foo.jpg&path[]=folder/
    public function delete(Request $request)
    {
        $paths = (array) $request->input('path', []);
        $fs = Storage::disk($this->disk);

        foreach ($paths as $rel) {
            // rel like "banners" or "banners/hero.jpg"
            $abs = $this->join($rel);

            // block escape
            if (!$this->isWithinBase($abs)) {
                return response()->json(['error' => 'Invalid path'], 422);
            }

            // delete file or directory (recursive)
            if ($fs->directoryExists($abs)) {
                $fs->deleteDirectory($abs);
            } elseif ($fs->fileExists($abs)) {
                $fs->delete($abs);
            }
        }

        return response()->json(['success' => 'Deleted']);
    }

    // ---------- helpers ----------

    private function sanitizeFileName(string $name): string
    {
        // remove disallowed chars (OpenCart regex parity)
        $clean = preg_replace('/[\/\\\\\?%*:|"<>]/', '', $name) ?? $name;
        // normalize spaces
        return trim($clean);
    }

    private function sanitizeFolderName(string $name): string
    {
        $clean = preg_replace('/[\/\\\\\?%*&:|"<>]/', '', $name) ?? $name;
        return trim($clean);
    }

    private function safeDirectory(string $dir): string
    {
        $dir = trim($dir, '/');
        // collapse .. and .
        $dir = Str::of($dir)->replace('\\', '/')->value();
        $dir = preg_replace('#\.+/#', '', $dir) ?? $dir;
        return trim($dir, '/'); // may be ''
    }

    private function join(string $relative): string
    {
        $relative = ltrim($relative, '/');  // may be ''
        return $this->baseDir . ($relative ? '/' . $relative : '');
    }

    private function isWithinBase(string $abs): bool
    {
        // on Storage disks this is logical; we only allow within baseDir by construction
        return Str::of($abs)->startsWith($this->baseDir);
    }

    private function relativeToBase(string $abs): string
    {
        return ltrim(Str::of($abs)->after($this->baseDir)->ltrim('/'), '/');
    }
}
