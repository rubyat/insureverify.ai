<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PagesController extends Controller
{
    public function dashboard(Request $request): Response
    {
        return Inertia::render('app/Dashboard', [
            'usage' => [
                'used' => 42,
                'limit' => 100,
                'resetDate' => now()->addDays(12)->toDateString(),
            ],
            'plan' => [
                'name' => 'Starter',
                'price' => 9.99,
                'upgradeUrl' => route('plans.index'),
            ],
            'recentUploads' => collect(range(1, 5))->map(fn ($i) => [
                'id' => $i,
                'thumbnail' => asset('images/hero-image.jpg'),
                'filename' => "image_{$i}.jpg",
                'size' => rand(120, 980) . ' KB',
                'status' => 'processed',
                'uploaded_at' => now()->subDays($i)->toDateTimeString(),
            ]),
            'banners' => [
                ['type' => 'warning', 'message' => 'You have used 42% of your monthly quota.'],
                ['type' => 'info', 'message' => 'Trial ends in 5 days.'],
            ],
        ]);
    }

    public function upload(Request $request): Response
    {
        return Inertia::render('app/Upload', [
            'remainingUploads' => 58,
            'cycleResetDate' => now()->addDays(12)->toDateString(),
            'queue' => [],
            'atLimit' => false,
            'upgradeUrl' => route('plans.index'),
        ]);
    }

    public function library(Request $request): Response
    {
        return Inertia::render('app/Library', [
            'filters' => [
                'search' => null,
                'date' => null,
                'status' => null,
            ],
            'items' => collect(range(1, 12))->map(fn ($i) => [
                'id' => $i,
                'thumbnail' => asset('images/hero-image.jpg'),
                'filename' => sprintf('upload_%02d.jpg', $i),
                'size' => rand(100, 2048) . ' KB',
                'status' => collect(['processed', 'queued', 'failed'])->random(),
                'url' => asset('images/hero-image.jpg'),
            ]),
            'empty' => false,
        ]);
    }

    public function usage(Request $request): Response
    {
        return Inertia::render('app/Usage', [
            'usage' => [
                'used' => 42,
                'limit' => 100,
                'resetDate' => now()->addDays(12)->toDateString(),
            ],
            'history' => [
                ['cycle' => 'Jun', 'used' => 80, 'limit' => 100],
                ['cycle' => 'Jul', 'used' => 55, 'limit' => 100],
                ['cycle' => 'Aug', 'used' => 42, 'limit' => 100],
            ],
            'upgradeUrl' => route('plans.index'),
        ]);
    }

    public function billing(Request $request): Response
    {
        return Inertia::render('app/Billing', [
            'currentPlan' => [
                'name' => 'Starter',
                'price' => 9.99,
                'renewalDate' => now()->addMonth()->toDateString(),
            ],
            'invoices' => [
                ['id' => 'inv_001', 'date' => now()->subMonths(2)->toDateString(), 'amount' => 9.99, 'url' => '#'],
                ['id' => 'inv_002', 'date' => now()->subMonth()->toDateString(), 'amount' => 9.99, 'url' => '#'],
            ],
            'paymentMethod' => [
                'brand' => 'Visa',
                'last4' => '4242',
                'exp' => '08/27',
            ],
            'errors' => [],
        ]);
    }

    public function notifications(Request $request): Response
    {
        return Inertia::render('app/Notifications', [
            'notifications' => [
                ['id' => 1, 'type' => 'quota', 'message' => 'You are nearing your quota for this cycle.', 'read' => false, 'date' => now()->subDays(1)->toDateTimeString()],
                ['id' => 2, 'type' => 'billing', 'message' => 'Your last payment succeeded.', 'read' => true, 'date' => now()->subDays(7)->toDateTimeString()],
                ['id' => 3, 'type' => 'announce', 'message' => 'New features launched!', 'read' => false, 'date' => now()->subDays(10)->toDateTimeString()],
            ],
        ]);
    }

    public function support(Request $request): Response
    {
        return Inertia::render('app/Support', [
            'faqs' => [
                ['q' => 'How do I upload images?', 'a' => 'Go to Upload and drag & drop your files.'],
                ['q' => 'How is usage calculated?', 'a' => 'Each uploaded image counts toward your monthly limit.'],
            ],
            'contact' => [
                'email' => 'support@example.com',
                'statusUrl' => 'https://status.example.com',
            ],
        ]);
    }
}
