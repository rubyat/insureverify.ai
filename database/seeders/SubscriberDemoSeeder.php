<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\SubscriptionUsage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SubscriberDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1) Ensure we have a plan to subscribe to
            $plan = Plan::query()->where('slug', 'gold')->first()
                ?? Plan::query()->where('is_active', true)->orderBy('sort_order')->first()
                ?? Plan::query()->first();

            if (! $plan) {
                // If no plans exist, bail out gracefully
                return;
            }

            // 2) Create a demo subscriber user
            $user = User::query()->updateOrCreate(
                ['email' => 'subscriber@example.com'],
                [
                    'first_name' => 'Demo',
                    'last_name'  => 'Subscriber',
                    'name'       => 'Demo Subscriber',
                    'password'   => Hash::make('password'),
                ]
            );

            // 3) Create a current active subscription with plan snapshot
            $now = now();
            $currentStart = $now->copy()->startOfMonth();
            $currentEnd   = $currentStart->copy()->addMonth();
            $prevStart    = $currentStart->copy()->subMonth();
            $prevEnd      = $currentStart->copy();

            $priceMonthlyCents = $plan->price !== null ? (int) round(((float) $plan->price) * 100) : 0;
            $includedVerifications = $plan->verifications_included ?? null;
            $overagePricePerUnitCents = 500; // $5.00 per extra verification (dummy)

            $subscription = Subscription::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
                [
                    'status'                          => 'active',
                    'trial_ends_at'                   => null,
                    'current_period_start'            => $currentStart,
                    'current_period_end'              => $currentEnd,
                    'renews_at'                       => $currentEnd,
                    'canceled_at'                     => null,
                    'cancel_at_period_end'            => false,
                    'currency'                        => 'USD',
                    'price_monthly_cents'             => $priceMonthlyCents,
                    'included_verifications'          => $includedVerifications,
                    'overage_price_per_unit_cents'    => $overagePricePerUnitCents,
                    'provider'                        => 'testpay',
                    'provider_customer_id'            => 'cus_'.Str::random(10),
                    'provider_subscription_id'        => 'sub_'.Str::random(12),
                    'metadata'                        => [
                        'note' => 'Demo seeded subscription',
                        'plan_snapshot' => [
                            'name' => $plan->name,
                            'slug' => $plan->slug,
                            'features' => $plan->features,
                        ],
                    ],
                ]
            );

            // 4) Usage: previous period and current period (metric = verifications)
            $prevUsed = $includedVerifications ? ($includedVerifications + 12) : 52; // ensure overage
            $currentUsed = 5;

            SubscriptionUsage::query()->updateOrCreate(
                [
                    'subscription_id' => $subscription->id,
                    'metric'          => 'verifications',
                    'period_start'    => $prevStart,
                    'period_end'      => $prevEnd,
                ],
                [
                    'used'                => $prevUsed,
                    'last_incremented_at' => $prevEnd->copy()->subDay(),
                ]
            );

            SubscriptionUsage::query()->updateOrCreate(
                [
                    'subscription_id' => $subscription->id,
                    'metric'          => 'verifications',
                    'period_start'    => $currentStart,
                    'period_end'      => $currentEnd,
                ],
                [
                    'used'                => $currentUsed,
                    'last_incremented_at' => $now,
                ]
            );

            // 5) Invoices: create a paid invoice for previous period, and a historical paid invoice for two months ago
            $baseFeeCents = $priceMonthlyCents;
            $included = $includedVerifications ?? 0;
            $prevOverageQty = max($prevUsed - $included, 0);
            $prevOverageCents = $prevOverageQty * $overagePricePerUnitCents;
            $prevSubtotal = $baseFeeCents + $prevOverageCents;
            $prevTax = (int) round($prevSubtotal * 0.0); // set to 0 for demo
            $prevTotal = $prevSubtotal + $prevTax;

            $prevInvoice = Invoice::query()->create([
                'subscription_id' => $subscription->id,
                'user_id'         => $user->id,
                'number'          => Invoice::generateInvoiceNumber(),
                'status'          => 'paid',
                'currency'        => 'USD',
                'subtotal_cents'  => $prevSubtotal,
                'discount_cents'  => 0,
                'tax_cents'       => $prevTax,
                'total_cents'     => $prevTotal,
                'period_start'    => $prevStart,
                'period_end'      => $prevEnd,
                'provider'        => 'testpay',
                'provider_invoice_id' => 'inv_'.Str::random(10),
                'metadata'        => ['note' => 'Auto-generated on renewal'],
                'issued_at'       => $prevEnd,
                'due_at'          => $prevEnd->copy()->addDays(7),
                'paid_at'         => $prevEnd->copy()->addDay(),
            ]);

            // Items for previous invoice
            InvoiceItem::query()->create([
                'invoice_id'         => $prevInvoice->id,
                'type'               => 'base_fee',
                'description'        => $plan->name.' monthly subscription',
                'quantity'           => 1,
                'unit_price_cents'   => $baseFeeCents,
                'amount_cents'       => $baseFeeCents,
                'metadata'           => ['plan_slug' => $plan->slug],
            ]);

            if ($prevOverageQty > 0) {
                InvoiceItem::query()->create([
                    'invoice_id'         => $prevInvoice->id,
                    'type'               => 'overage',
                    'description'        => 'Overage verifications',
                    'quantity'           => $prevOverageQty,
                    'unit_price_cents'   => $overagePricePerUnitCents,
                    'amount_cents'       => $prevOverageCents,
                    'metadata'           => ['metric' => 'verifications'],
                ]);
            }

            // Payment for previous invoice
            Payment::query()->create([
                'invoice_id'                  => $prevInvoice->id,
                'provider'                    => 'testpay',
                'provider_payment_intent_id'  => 'pi_'.Str::random(14),
                'status'                      => 'succeeded',
                'amount_cents'                => $prevTotal,
                'currency'                    => 'USD',
                'paid_at'                     => $prevEnd->copy()->addDay(),
            ]);

            // Historical invoice: two months ago
            $histStart = $prevStart->copy()->subMonth();
            $histEnd   = $prevStart->copy();
            $histUsed  = $included ? (int) floor($included * 0.8) : 20;
            $histOver  = max($histUsed - $included, 0);
            $histSubtotal = $baseFeeCents + ($histOver * $overagePricePerUnitCents);
            $histTotal = $histSubtotal;

            $histInvoice = Invoice::query()->create([
                'subscription_id' => $subscription->id,
                'user_id'         => $user->id,
                'number'          => Invoice::generateInvoiceNumber(),
                'status'          => 'paid',
                'currency'        => 'USD',
                'subtotal_cents'  => $histSubtotal,
                'discount_cents'  => 0,
                'tax_cents'       => 0,
                'total_cents'     => $histTotal,
                'period_start'    => $histStart,
                'period_end'      => $histEnd,
                'provider'        => 'testpay',
                'provider_invoice_id' => 'inv_'.Str::random(10),
                'metadata'        => ['note' => 'Auto-generated on renewal'],
                'issued_at'       => $histEnd,
                'due_at'          => $histEnd->copy()->addDays(7),
                'paid_at'         => $histEnd->copy()->addDay(),
            ]);

            InvoiceItem::query()->create([
                'invoice_id'         => $histInvoice->id,
                'type'               => 'base_fee',
                'description'        => $plan->name.' monthly subscription',
                'quantity'           => 1,
                'unit_price_cents'   => $baseFeeCents,
                'amount_cents'       => $baseFeeCents,
                'metadata'           => ['plan_slug' => $plan->slug],
            ]);

            if ($histOver > 0) {
                InvoiceItem::query()->create([
                    'invoice_id'         => $histInvoice->id,
                    'type'               => 'overage',
                    'description'        => 'Overage verifications',
                    'quantity'           => $histOver,
                    'unit_price_cents'   => $overagePricePerUnitCents,
                    'amount_cents'       => $histOver * $overagePricePerUnitCents,
                    'metadata'           => ['metric' => 'verifications'],
                ]);
            }

            Payment::query()->create([
                'invoice_id'                  => $histInvoice->id,
                'provider'                    => 'testpay',
                'provider_payment_intent_id'  => 'pi_'.Str::random(14),
                'status'                      => 'succeeded',
                'amount_cents'                => $histTotal,
                'currency'                    => 'USD',
                'paid_at'                     => $histEnd->copy()->addDay(),
            ]);

            // 6) Events timeline
            $events = [
                [
                    'event'      => 'subscription.created',
                    'old'        => null,
                    'new'        => [
                        'status' => 'active',
                        'plan'   => $plan->slug,
                    ],
                    'ts'         => $histStart->copy()->subDay(),
                ],
                [
                    'event'      => 'period_renewed',
                    'old'        => ['period_end' => $histEnd->toIso8601String()],
                    'new'        => ['period_end' => $prevEnd->toIso8601String()],
                    'ts'         => $histEnd,
                ],
                [
                    'event'      => 'invoice_generated',
                    'old'        => null,
                    'new'        => ['invoice_number' => $histInvoice->number],
                    'ts'         => $histEnd,
                ],
                [
                    'event'      => 'payment_succeeded',
                    'old'        => null,
                    'new'        => ['invoice_number' => $histInvoice->number],
                    'ts'         => $histEnd->copy()->addDay(),
                ],
                [
                    'event'      => 'usage_incremented',
                    'old'        => ['used' => $histUsed - 1],
                    'new'        => ['used' => $histUsed],
                    'ts'         => $histEnd->copy()->subDays(10),
                ],
                [
                    'event'      => 'period_renewed',
                    'old'        => ['period_end' => $prevEnd->toIso8601String()],
                    'new'        => ['period_end' => $currentEnd->toIso8601String()],
                    'ts'         => $prevEnd,
                ],
                [
                    'event'      => 'invoice_generated',
                    'old'        => null,
                    'new'        => ['invoice_number' => $prevInvoice->number],
                    'ts'         => $prevEnd,
                ],
                [
                    'event'      => 'payment_succeeded',
                    'old'        => null,
                    'new'        => ['invoice_number' => $prevInvoice->number],
                    'ts'         => $prevEnd->copy()->addDay(),
                ],
                [
                    'event'      => 'usage_incremented',
                    'old'        => ['used' => $currentUsed - 1],
                    'new'        => ['used' => $currentUsed],
                    'ts'         => $now,
                ],
            ];

            foreach ($events as $e) {
                SubscriptionEvent::query()->create([
                    'subscription_id' => $subscription->id,
                    'actor_user_id'   => $user->id,
                    'event'           => $e['event'],
                    'old_values'      => $e['old'],
                    'new_values'      => $e['new'],
                    'metadata'        => null,
                    'created_at'      => $e['ts'],
                ]);
            }
        });
    }
}
