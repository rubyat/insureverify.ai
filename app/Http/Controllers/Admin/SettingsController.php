<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use App\Services\FileManagerService;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{

    public function __construct(private FileManagerService $fm)
    {
    }

    public function index(Request $request): Response
    {
        $groups = Setting::query()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('code')
            ->map(function ($items) {
                return $items->map(function (Setting $s) {
                    $value = $s->valueDecoded;
                    $meta = $s->meta ?? [];
                    if ($s->type === 'file' && is_string($value) && $value !== '') {
                        // Generate a placeholder thumbnail for file settings
                        $meta['placeholder'] = $this->fm->thumb($value, 300, 300);
                    }
                    return [
                        'id' => $s->id,
                        'label' => $s->label,
                        'key' => $s->key,
                        'type' => $s->type,
                        'value' => $value,
                        'meta' => $meta,
                        'sort_order' => $s->sort_order,
                    ];
                })->values();
            });

        return Inertia::render('Admin/settings/Index', [
            'groups' => $groups,
            'codes' => $groups->keys()->values(),
        ]);
    }

    public function update(Request $request)
    {
        // Expecting payload { settings: { [code]: [ {id,key,type,value|null} ] } }
        // When posting via FormData, "settings" will be a JSON string
        $payloadRaw = $request->input('settings');
        if (is_string($payloadRaw)) {
            $payload = json_decode($payloadRaw, true) ?? [];
        } else {
            $payload = is_array($payloadRaw) ? $payloadRaw : [];
        }

        foreach ($payload as $code => $items) {
            foreach ($items as $item) {
                // For file inputs, check for uploaded file by key
                $id = Arr::get($item, 'id');
                $type = Arr::get($item, 'type');
                /** @var Setting|null $setting */
                $setting = Setting::query()->find($id);
                if (!$setting) continue;

                if ($type === 'file') {
                    // Accept value from JSON payload (e.g., from ImagePicker)
                    $path = Arr::get($item, 'value');
                    if (is_string($path)) {
                        $setting->value = $path;
                        $setting->save();
                    }
                    continue;
                }

                $value = Arr::get($item, 'value');
                $setting->value = is_array($value) ? $value : (string) ($value ?? '');
                $setting->save();
            }
        }

        // Invalidate the cached settings used by SettingsServiceProvider
        Cache::forget('app_settings_config');

        return back()->with('success', 'Settings saved');
    }
}
