<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\UserCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $subscription = $user?->activeSubscription();
        $plan = $user?->currentPlan();

        $currentPlan = [
            'name' => $plan?->name ?? '—',
            'price' => $plan?->price ? (float) $plan->price : 0.0,
            'renewalDate' => $subscription?->renews_at?->toDateString() ?? $subscription?->current_period_end?->toDateString() ?? now()->addMonth()->toDateString(),
        ];

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

        $cards = $user?->cards()->orderByDesc('is_default')->orderBy('id')->get()->map(function (UserCard $c) {
            return [
                'id' => $c->id,
                'brand' => $c->brand,
                'last4' => $c->last4,
                'exp' => str_pad((string) $c->exp_month, 2, '0', STR_PAD_LEFT) . '/' . substr((string) $c->exp_year, -2),
                'name' => trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')),
                'is_default' => (bool) $c->is_default,
            ];
        }) ?? collect();

        $default = $user?->cards()->where('is_default', true)->first() ?? $user?->cards()->latest()->first();
        $paymentMethod = [
            'brand' => $default->brand ?? '—',
            'last4' => $default->last4 ?? '----',
            'exp' => isset($default)
                ? str_pad((string) $default->exp_month, 2, '0', STR_PAD_LEFT) . '/' . substr((string) $default->exp_year, -2)
                : '—',
        ];

        return Inertia::render('app/Billing', [
            'currentPlan' => $currentPlan,
            'invoices' => $invoices,
            'paymentMethod' => $paymentMethod,
            'cards' => $cards,
            'errors' => [],
        ]);
    }

    public function storeCard(Request $request)
    {
        $user = $request->user();

        // Accept either detailed fields OR signup-style fields
        $input = $request->all();

        // If coming from Signup-style modal: card, exp, cvv
        if (!empty($input['card']) && !empty($input['exp'])) {
            $number = preg_replace('/\D+/', '', (string) $input['card']);
            $last4 = substr($number, -4) ?: null;

            $exp = preg_replace('/\D+/', '', (string) $input['exp']);
            $expMonth = null;
            $expYear = null;
            if (strlen($exp) === 4) { // MMYY
                $expMonth = (int) substr($exp, 0, 2);
                $yy = (int) substr($exp, 2, 2);
                $expYear = 2000 + $yy;
            } elseif (strlen($exp) === 6) { // MMYYYY
                $expMonth = (int) substr($exp, 0, 2);
                $expYear = (int) substr($exp, 2, 4);
            }

            $brand = $this->detectBrand($number);
            $token = 'card_' . (string) \Illuminate\Support\Str::uuid();

            $data = [
                'first_name' => $input['first_name'] ?? $user->first_name,
                'last_name' => $input['last_name'] ?? $user->last_name,
                'brand' => $brand ?? 'Card',
                'last4' => $last4,
                'exp_month' => $expMonth,
                'exp_year' => $expYear,
                'token' => $token,
            ];

            $validated = Validator::make($data, [
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'brand' => ['required', 'string', 'max:50'],
                'last4' => ['required', 'digits:4'],
                'exp_month' => ['required', 'integer', 'between:1,12'],
                'exp_year' => ['required', 'integer', 'min:' . now()->year, 'max:' . (now()->year + 15)],
                'token' => ['nullable', 'string', 'max:255'],
            ])->validate();
        } else {
            // Detailed fields path
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'brand' => ['required', 'string', 'max:50'],
                'last4' => ['required', 'digits:4'],
                'exp_month' => ['required', 'integer', 'between:1,12'],
                'exp_year' => ['required', 'integer', 'min:' . now()->year, 'max:' . (now()->year + 15)],
                'token' => ['nullable', 'string', 'max:255'],
            ]);
        }

        DB::transaction(function () use ($user, $validated) {
            $hasAny = $user->cards()->exists();
            $card = new UserCard(array_merge($validated, [
                'user_id' => $user->id,
                'is_default' => !$hasAny, // first card becomes default
            ]));
            $card->save();
        });

        return back()->with('success', 'Card added.');
    }

    private function detectBrand(string $number): ?string
    {
        // Basic BIN pattern detection (non-exhaustive)
        if (preg_match('/^4[0-9]{6,}$/', $number)) return 'Visa';
        if (preg_match('/^(5[1-5][0-9]{5,}|2(2[2-9][0-9]{4,}|[3-6][0-9]{5,}|7[01][0-9]{5,}|720[0-9]{4,}))$/', $number)) return 'Mastercard';
        if (preg_match('/^3[47][0-9]{5,}$/', $number)) return 'American Express';
        if (preg_match('/^6(?:011|5[0-9]{2})[0-9]{3,}$/', $number)) return 'Discover';
        return 'Card';
    }

    public function makeDefault(Request $request, UserCard $card)
    {
        $user = $request->user();
        abort_if($card->user_id !== $user->id, 403);

        DB::transaction(function () use ($user, $card) {
            $user->cards()->update(['is_default' => false]);
            $card->update(['is_default' => true]);
        });

        return back()->with('success', 'Default card updated.');
    }

    public function destroyCard(Request $request, UserCard $card)
    {
        $user = $request->user();
        abort_if($card->user_id !== $user->id, 403);
        if ($card->is_default) {
            return back()->with('error', 'Cannot delete the default card.');
        }
        $card->delete();
        return back()->with('success', 'Card deleted.');
    }
}
