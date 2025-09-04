<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BlogPublicController;

// Marketing and public routes moved to routes/frontend.php


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Frontend (customer) routes
require __DIR__.'/frontend.php';

// Admin and Super Admin routes
require __DIR__.'/admin.php';

// Filemanager routes
require __DIR__.'/filemanager.php';

// Public plans listing moved to routes/frontend.php

// Subscription and other authenticated customer routes moved to routes/frontend.php


Route::get('/blog/category/{category}', [BlogPublicController::class, 'byCategory'])->name('blog.category');
Route::get('/blog/tag/{tag}', [BlogPublicController::class, 'byTag'])->name('blog.tag');
Route::get('/blog/{slug}', [BlogPublicController::class, 'show'])->name('blog.show');

Route::get('/_setup/storage-link', function () {

    // Skip if already linked
    if (file_exists(public_path('storage'))) {
        return response('public/storage already exists', 200);
    }

    // Run the artisan command (flags are options)
    Artisan::call('storage:link', [
        '--relative' => true,   // optional
        '--force'    => true,   // optional in newer Laravel
    ]);

    return response(Artisan::output() ?: 'Symlink created', 201);
});


Route::get('/_ops/prime', function () {
    $results = [];

    $run = function (string $command, array $params = []) use (&$results) {
        try {
            $code = Artisan::call($command, $params);
            $results[] = [
                'command'  => $command,
                'exitCode' => $code,
                'output'   => trim(Artisan::output()),
            ];
        } catch (\Throwable $e) {
            $results[] = [
                'command' => $command,
                'error'   => $e->getMessage(),
            ];
        }
    };

    // Run all the things
    $run('storage:link');     // or ['--force' => true] if you want overwrite behavior
    $run('config:cache');
    $run('view:cache');

    // NOTE: route:cache will FAIL if any route uses a Closure (including THIS one).
    // Leave it here so you can see the error in the JSON and then switch to controller version below.
    $run('route:cache');

    return response()->json([
        'warning' => 'Do NOT expose this publicly in prod.',
        'results' => $results,
    ]);
});
