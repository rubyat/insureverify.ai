<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // High-level metrics
        $totalUsers = User::query()->count();
        $activeSubscriptions = Subscription::query()->active()->count();
        $mrrCents = (int) Subscription::query()->active()->sum('price_monthly_cents');
        $planCount = Plan::query()->count();

        // Payments (only successful)
        $paidStatuses = ['paid', 'succeeded'];
        $todayStart = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $todayPaymentsCents = (int) Payment::query()
            ->whereIn('status', $paidStatuses)
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $todayStart)
            ->sum('amount_cents');

        $weekPaymentsCents = (int) Payment::query()
            ->whereIn('status', $paidStatuses)
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $weekStart)
            ->sum('amount_cents');

        $monthPaymentsCents = (int) Payment::query()
            ->whereIn('status', $paidStatuses)
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $monthStart)
            ->sum('amount_cents');

        // Recent activity
        $recentInvoices = Invoice::query()
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'number', 'total_cents', 'currency', 'status', 'issued_at'])
            ->map(fn($inv) => [
                'id' => $inv->id,
                'number' => $inv->number,
                'total' => ($inv->total_cents ?? 0) / 100,
                'currency' => $inv->currency,
                'status' => $inv->status,
                'issued_at' => optional($inv->issued_at)?->toDateTimeString(),
            ]);

        $recentUsers = User::query()
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'created_at' => optional($u->created_at)?->toDateTimeString(),
            ]);

        // Last 7 days verifications (using SubscriptionUsage metric='verifications')
        $metric = 'verifications';
        $days = collect(range(0, 6))->map(fn($i) => Carbon::today()->subDays(6 - $i));
        $usageRows = SubscriptionUsage::query()
            ->where('metric', $metric)
            ->where('last_incremented_at', '>=', $days->first()->startOfDay())
            ->get(['used', 'last_incremented_at']);
        $byDate = [];
        foreach ($usageRows as $row) {
            $d = Carbon::parse($row->last_incremented_at)->format('Y-m-d');
            $byDate[$d] = ($byDate[$d] ?? 0) + (int) $row->used;
        }
        $verificationsLast7 = $days->map(fn($d) => [
            'date' => $d->format('Y-m-d'),
            'count' => (int) ($byDate[$d->format('Y-m-d')] ?? 0),
        ]);

        return Inertia::render('Admin/Dashboard', [
            'cards' => [
                'total_users' => $totalUsers,
                'active_subscriptions' => $activeSubscriptions,
                'mrr' => $mrrCents / 100,
                'plans' => $planCount,
                'payments_today' => $todayPaymentsCents / 100,
                'payments_week' => $weekPaymentsCents / 100,
                'payments_month' => $monthPaymentsCents / 100,
            ],
            'recent_invoices' => $recentInvoices,
            'recent_signups' => $recentUsers,
            'verifications_last7' => $verificationsLast7,
        ]);
    }
}
