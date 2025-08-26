<?php

namespace App\Http\Controllers;

use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    public function __construct(private FileManagerService $fm)
    {
    }

    public function index(Request $request)
    {
        $directory = (string) $request->query('directory', '');
        $filter = (string) $request->query('filter_name', '');
        $page = (int) $request->query('page', 1);
        $limit = (int) $request->query('limit', config('filemanager.limit', 12));

        $data = $this->fm->list($directory, $filter, $page, $limit);

        // add thumbs and href (full url) like OC
        $thumbSize = explode('x', config('filemanager.thumb_size', '300x300'));
        $w = (int) ($thumbSize[0] ?? 300);
        $h = (int) ($thumbSize[1] ?? 300);

        $files = [];
        foreach ($data['files'] as $f) {
            $thumb = $this->fm->thumb($f['path'], $w, $h);
            $files[] = [
                'name' => $f['name'],
                'path' => $f['path'],
                'thumb' => $thumb,
                'href' => $thumb,
            ];
        }

        return response()->json([
            'directories' => $data['directories'],
            'images' => $files,
            'pagination' => $data['pagination'],
            'directory' => $data['directory'],
            'filter_name' => $data['filter_name'],
        ]);
    }

    private function filePublicUrl(string $relative): string
    {
        $disk = config('filemanager.disk', 'public');
        $base = trim(config('filemanager.base', 'catalog'), '/');
        $path = $base . '/' . ltrim($relative, '/');
        return Storage::disk($disk)->url($path);
    }

    public function upload(Request $request)
    {
        $directory = (string) $request->query('directory', '');
        $files = $request->file('file');
        if (!$files) {
            return response()->json(['error' => 'No files uploaded'], 422);
        }
        if ($files instanceof UploadedFile) {
            $files = [$files];
        }
        $result = $this->fm->upload($files, $directory);
        $status = isset($result['error']) ? 422 : 200;
        return response()->json($result, $status);
    }

    public function folder(Request $request)
    {
        $directory = (string) $request->query('directory', '');
        $folder = (string) $request->input('folder', '');
        $result = $this->fm->createFolder($folder, $directory);
        $status = isset($result['error']) ? 422 : 200;
        return response()->json($result, $status);
    }

    public function delete(Request $request)
    {
        $paths = $request->input('path', []);
        if (!is_array($paths)) $paths = [$paths];
        $result = $this->fm->delete($paths);
        $status = isset($result['error']) ? 422 : 200;
        return response()->json($result, $status);
    }
}
