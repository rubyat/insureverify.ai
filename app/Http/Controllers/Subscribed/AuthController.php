<?php

namespace App\Http\Controllers\Subscribed;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCard;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\SubscriptionEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Show the marketing signup page.
     */
    public function showSignup(): Response
    {
        return Inertia::render('Signup');
    }

    /**
     * Handle marketing signup form submit.
     */
    public function signup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', 'string'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
            // Payment placeholders from the marketing form; not used by backend yet
            'card' => ['nullable', 'string'],
            'exp' => ['nullable', 'string'],
            'cvv' => ['nullable', 'string'],
        ]);

        // Create user and assign default subscriber role
        $user = User::create([
            'name' => trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? '')),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole('subscriber');

        event(new Registered($user));
        Auth::login($user);

        // If card details are provided, persist a default user card
        if (! empty($validated['card']) && ! empty($validated['exp'])) {
            $number = preg_replace('/\D+/', '', (string) $validated['card']);
            $last4 = substr($number, -4) ?: null;

            // Parse exp as MMYY or MM/YY
            $exp = preg_replace('/\D+/', '', (string) $validated['exp']);
            $expMonth = null;
            $expYear = null;
            if (strlen($exp) === 4) {
                $expMonth = (int) substr($exp, 0, 2);
                $yy = (int) substr($exp, 2, 2);
                $expYear = 2000 + $yy;
            } elseif (strlen($exp) === 6) { // MMYYYY
                $expMonth = (int) substr($exp, 0, 2);
                $expYear = (int) substr($exp, 2, 4);
            }

            $brand = self::detectBrand($number);
            $token = 'card_' . Str::uuid()->toString();

            if ($last4 && $expMonth && $expYear) {
                UserCard::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'] ?? $user->first_name,
                    'last_name' => $validated['last_name'] ?? $user->last_name,
                    'brand' => $brand,
                    'last4' => $last4,
                    'exp_month' => $expMonth,
                    'exp_year' => $expYear,
                    'token' => $token,
                    'is_default' => true,
                ]);
            }
        }

        // Create active subscription immediately (payment to be added later)
        // Resolve selected plan by slug or ID
        $plan = Plan::query()
            ->where('slug', $validated['plan'])
            ->orWhere('id', $validated['plan'])
            ->firstOrFail();

        DB::transaction(function () use ($user, $plan) {
            $now = now();
            $periodStart = $now;
            $periodEnd = (clone $now)->addMonth();

            $priceMonthlyCents = $plan->price !== null ? (int) round(((float) $plan->price) * 100) : 0;

            // Create subscription with plan snapshots
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active', // later: support trialing, etc.
                'trial_ends_at' => null,
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'renews_at' => $periodEnd,
                'canceled_at' => null,
                'cancel_at_period_end' => false,
                'currency' => 'USD',
                'price_monthly_cents' => $priceMonthlyCents,
                'included_verifications' => $plan->verifications_included,
                'overage_price_per_unit_cents' => null,
                'provider' => null,
                'provider_customer_id' => null,
                'provider_subscription_id' => null,
                'metadata' => [
                    'plan_slug' => $plan->slug,
                    'created_via' => 'signup',
                ],
            ]);

            // Initialize usage for current period
            SubscriptionUsage::create([
                'subscription_id' => $subscription->id,
                'metric' => 'verifications',
                'used' => 0,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'last_incremented_at' => null,
            ]);

            // Log subscription created event
            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'actor_user_id' => $user->id,
                'event' => 'created',
                'old_values' => null,
                'new_values' => [
                    'status' => 'active',
                    'plan_id' => $plan->id,
                    'current_period_start' => $periodStart,
                    'current_period_end' => $periodEnd,
                ],
                'metadata' => [
                    'snapshot' => [
                        'price_monthly_cents' => $priceMonthlyCents,
                        'included_verifications' => $plan->verifications_included,
                    ],
                ],
                'created_at' => now(),
            ]);
        });

        // Redirect user to dashboard after successful signup + subscription creation
        return redirect()->route('app.dashboard');
    }

    /**
     * Naive brand detection from card number prefix.
     */
    protected static function detectBrand(?string $number): ?string
    {
        if (! $number) return null;
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $number)) return 'visa';
        if (preg_match('/^(5[1-5][0-9]{14})$/', $number)) return 'mastercard';
        if (preg_match('/^(3[47][0-9]{13})$/', $number)) return 'amex';
        if (preg_match('/^(6011|65|64[4-9])[0-9]{12,15}$/', $number)) return 'discover';
        if (preg_match('/^(352[89]|35[3-8][0-9])[0-9]{12}$/', $number)) return 'jcb';
        if (preg_match('/^(30[0-5]|36|38)[0-9]{12}$/', $number)) return 'diners';
        return null;
    }

    /**
     * Optional: show login page (existing auth routes already provide this)
     */
    public function showLogin(): Response
    {
        return Inertia::render('auth/Login');
    }

    /**
     * Optional: handle login (prefer using built-in Auth routes)
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
