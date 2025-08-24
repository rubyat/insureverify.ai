<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\SubscriptionUsage;
use App\Models\Invoice;
use App\Models\UserCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PagesController extends Controller
{
    public function dashboard(Request $request): Response
    {
        $user = $request->user();

        $subscription = $user?->activeSubscription();
        $plan = $user?->currentPlan();

        // Determine current period usage for metric 'verifications' via model helpers
        $used = 0;
        $limit = 0;
        $resetDate = now()->addDays(30)->toDateString();
        if ($subscription) {
            $used = $subscription->usedInCurrentPeriod('verifications');
            $limit = $subscription->includedVerificationLimit();
            $resetDate = $subscription->cycleResetDate() ?? $resetDate;
        } else {
            $limit = (int) ($plan?->verifications_included ?? 0);
        }

        // Recent uploads from the user's images
        $recentUploads = $user
            ? $user->images()->latest()->limit(5)->get()->map(function (Image $img) {
                $url = $img->path
                    ? (Str::startsWith($img->path, ['http://', 'https://'])
                        ? $img->path
                        : (Storage::disk('public')->exists($img->path)
                            ? Storage::url($img->path)
                            : asset('images/hero-image.jpg')))
                    : asset('images/hero-image.jpg');
                $filename = basename($img->path ?? 'upload.jpg');
                return [
                    'id' => $img->id,
                    'thumbnail' => $url,
                    'filename' => $filename,
                    'size' => '',
                    'status' => 'processed',
                    'uploaded_at' => optional($img->created_at)?->toDateTimeString(),
                ];
            })
            : collect();

        // Banners based on usage and trial status
        $banners = [];
        if ($limit > 0) {
            $pct = $used / max($limit, 1);
            if ($pct >= 0.9) {
                $banners[] = ['type' => 'warning', 'message' => 'You have used '.round($pct * 100)."% of your monthly quota."];
            } elseif ($pct >= 0.7) {
                $banners[] = ['type' => 'info', 'message' => 'You have used '.round($pct * 100)."% of your monthly quota."];
            }
        }
        if ($subscription && $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()) {
            $days = now()->diffInDays($subscription->trial_ends_at);
            $banners[] = ['type' => 'info', 'message' => "Trial ends in {$days} day".($days === 1 ? '' : 's').'.'];
        }

        return Inertia::render('app/Dashboard', [
            'usage' => [
                'used' => $used,
                'limit' => max($limit, 1), // avoid divide-by-zero on frontend
                'resetDate' => $resetDate,
            ],
            'plan' => [
                'name' => $plan?->name ?? '—',
                'price' => $plan?->price ? (float) $plan->price : 0.0,
                'upgradeUrl' => route('plans.index'),
            ],
            'recentUploads' => $recentUploads,
            'banners' => $banners,
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
        $user = $request->user();
        $subscription = $user?->activeSubscription();
        $plan = $user?->currentPlan();

        // Current cycle usage
        $used = 0;
        $limit = 0;
        $resetDate = now()->addDays(30)->toDateString();
        if ($subscription) {
            $used = $subscription->usedInCurrentPeriod('verifications');
            $limit = $subscription->includedVerificationLimit();
            $resetDate = $subscription->cycleResetDate() ?? $resetDate;
        } else {
            $limit = (int) ($plan?->verifications_included ?? 0);
        }

        // History from past cycles (last 6 entries)
        $history = [];
        if ($subscription) {
            $rows = SubscriptionUsage::query()
                ->where('subscription_id', $subscription->id)
                ->where('metric', 'verifications')
                ->orderByDesc('period_end')
                ->limit(6)
                ->get();
            $history = $rows->sortBy('period_end')->values()->map(function ($row) use ($limit) {
                return [
                    'cycle' => optional($row->period_end)?->format('M') ?? '',
                    'used'  => (int) $row->used,
                    'limit' => max((int) $limit, 1),
                ];
            });
        }

        return Inertia::render('app/Usage', [
            'usage' => [
                'used' => $used,
                'limit' => max($limit, 1),
                'resetDate' => $resetDate,
            ],
            'history' => $history,
            'upgradeUrl' => route('plans.index'),
        ]);
    }

    public function billing(Request $request): Response
    {
        $user = $request->user();
        $subscription = $user?->activeSubscription();
        $plan = $user?->currentPlan();

        $currentPlan = [
            'name' => $plan?->name ?? '—',
            'price' => $plan?->price ? (float) $plan->price : 0.0,
            'renewalDate' => $subscription?->renews_at?->toDateString() ?? $subscription?->current_period_end?->toDateString() ?? now()->addMonth()->toDateString(),
        ];

        // Recent invoices (up to 12)
        $invoices = $user
            ? $user->invoices()->with('payments')->orderByDesc('issued_at')->orderByDesc('id')->limit(12)->get()->map(function (Invoice $inv) {
                return [
                    'id' => $inv->number,
                    'date' => ($inv->issued_at ?? $inv->created_at)?->toDateString(),
                    'amount' => (float) ($inv->total_cents / 100),
                    'url' => '#',
                ];
            })
            : collect();

        // Default payment method (UserCard)
        $card = $user?->cards()->where('is_default', true)->first() ?? $user?->cards()->latest()->first();
        $paymentMethod = [
            'brand' => $card->brand ?? '—',
            'last4' => $card->last4 ?? '----',
            'exp' => isset($card)
                ? str_pad((string) $card->exp_month, 2, '0', STR_PAD_LEFT) . '/' . substr((string) $card->exp_year, -2)
                : '—',
        ];

        return Inertia::render('app/Billing', [
            'currentPlan' => $currentPlan,
            'invoices' => $invoices,
            'paymentMethod' => $paymentMethod,
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
