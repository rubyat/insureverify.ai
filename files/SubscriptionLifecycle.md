## Lifecycle & “How it works”

1. **Create subscription**
   - On checkout, create `subscriptions` with `status='active'` (or `trialing`).
   - Set `current_period_start = now()`, `current_period_end = now()->addMonth()`, `renews_at = current_period_end`.
   - Copy plan snapshots (`price_monthly_cents`, `included_verifications`, optional `overage_price_per_unit_cents`).
   - Insert `subscription_usages` row for `metric='verifications'` for the current period with `used=0`.
   - Log `subscription_events.created`.

2. **Meter usage**
   - Each time a verification is started (or completed), **increment** the `subscription_usages.used`.
   - If `used >= included_verifications`, either **block** new verifications or allow **overage**.
   - Log `subscription_events.usage_incremented`.

3. **Renewal (monthly cron/queue)**
   - On `current_period_end`, generate an **invoice** for base fee + any **overages**:
     - Add `invoice_items`: `base_fee = price_monthly_cents`, `overage = max(used - included, 0) * overage_price_per_unit_cents`.
     - Mark invoice `open`, attempt **payment** via provider; update `payments` and invoice `status`.
     - If payment fails: set subscription `status='past_due'` and start dunning.
   - **Advance period**: set `current_period_start = current_period_end`, `current_period_end = current_period_start->addMonth()`, refresh `renews_at`.
   - **Reset usage**: create new `subscription_usages` row with `used=0` for the new period.
   - Log `subscription_events.period_renewed` and `invoice_generated`.

4. **Cancellation**
   - If `cancel_at_period_end = true`, keep running until `current_period_end`, then mark `status='canceled'`, set `canceled_at`.
   - If immediate cancel: set `status='canceled'`, disable new usage, pro-rate refund if needed (create credit `invoice_items`).
   - Log `subscription_events.canceled`.

5. **Plan change (upgrade/downgrade)**
   - Write `subscription_events.plan_changed` with `old_values/new_values`.
   - Choose your approach:
     - **Proration mid-cycle** (create interim invoice for difference).
     - **Defer change** to next cycle (simpler).
