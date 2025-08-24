<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
  cards: {
    total_users: number;
    active_subscriptions: number;
    mrr: number;
    plans: number;
    payments_today: number;
    payments_week: number;
    payments_month: number;
  };
  recent_invoices: Array<{ id: number; number: string; total: number; currency: string; status: string; issued_at: string | null }>;
  recent_signups: Array<{ id: number; name: string | null; email: string; created_at: string | null }>;
  verifications_last7: Array<{ date: string; count: number }>;
}>();

const breadcrumbs = [{ title: 'Admin', href: '/admin' }, { title: 'Dashboard', href: '/admin/dashboard' }];

const kpis = computed(() => [
  {
    label: 'Total Users',
    value: props.cards?.total_users ?? 0,
    icon: 'users',
    gradient: 'from-indigo-500/10 via-indigo-500/5 to-transparent',
  },
  {
    label: 'Active Subscriptions',
    value: props.cards?.active_subscriptions ?? 0,
    icon: 'sparkles',
    gradient: 'from-emerald-500/10 via-emerald-500/5 to-transparent',
  },
  {
    label: 'MRR',
    value: `$${(props.cards?.mrr ?? 0).toFixed(2)}`,
    icon: 'cash',
    gradient: 'from-amber-500/10 via-amber-500/5 to-transparent',
  },
]);

const paymentKpis = computed(() => [
  { label: "Plans", value: props.cards?.plans ?? 0 },
  { label: "Today's Payments", value: `$${(props.cards?.payments_today ?? 0).toFixed(2)}` },
  { label: "This Week", value: `$${(props.cards?.payments_week ?? 0).toFixed(2)}` },
  { label: "This Month", value: `$${(props.cards?.payments_month ?? 0).toFixed(2)}` },
]);

const chart = computed(() => {
  const data = props.verifications_last7 || [];
  const max = Math.max(1, ...data.map(d => d.count));
  // Prepare scaled bars (0..100)
  const bars = data.map(d => ({
    date: d.date,
    count: d.count,
    h: Math.round((d.count / max) * 100),
    label: new Date(d.date + 'T00:00:00').toLocaleDateString(undefined, { month: 'short', day: 'numeric' }),
  }));
  return { max, bars };
});

function statusClasses(status: string) {
  const s = (status || '').toLowerCase();
  if (s.includes('paid') || s.includes('succeeded')) return 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200';
  if (s.includes('open') || s.includes('pending')) return 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200';
  if (s.includes('failed') || s.includes('void')) return 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200';
  return 'bg-slate-50 text-slate-700 ring-1 ring-inset ring-slate-200';
}

function initials(name?: string | null, email?: string) {
  const base = (name && name.trim()) || (email || '').split('@')[0];
  const parts = base.split(/\s+/).filter(Boolean);
  return (parts[0]?.[0] || '').toUpperCase() + (parts[1]?.[0] || '').toUpperCase();
}
</script>

<template>
  <Head title="Admin · Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-8">
      <!-- Header -->
      <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">Admin Dashboard</h1>
          <p class="text-sm text-muted-foreground">Key metrics and recent activity across your platform</p>
        </div>

      <!-- Plans & Payments -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="(item, idx) in paymentKpis" :key="idx" class="rounded-xl border bg-white p-5 shadow-sm transition hover:shadow-md dark:bg-zinc-900">
          <div class="text-sm text-muted-foreground">{{ item.label }}</div>
          <div class="mt-1 text-2xl font-semibold">{{ item.value }}</div>
        </div>
      </div>
      </div>

      <!-- KPI Cards -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="(kpi, i) in kpis" :key="i" class="group relative overflow-hidden rounded-xl border bg-white p-5 shadow-sm transition hover:shadow-md dark:bg-zinc-900">
          <div :class="['pointer-events-none absolute inset-0 bg-gradient-to-br', kpi.gradient]"></div>
          <div class="relative flex items-start justify-between">
            <div>
              <div class="text-sm text-muted-foreground">{{ kpi.label }}</div>
              <div class="mt-1 text-3xl font-semibold">{{ kpi.value }}</div>
            </div>
            <div class="rounded-lg bg-white/70 p-2 ring-1 ring-inset ring-black/5 backdrop-blur group-hover:scale-105 transition">
              <!-- Simple inline icons -->
              <svg v-if="kpi.icon==='users'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-indigo-600"><path d="M16 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4Zm-8 0c1.657 0 3-1.79 3-4S9.657 3 8 3 5 4.79 5 7s1.343 4 3 4Zm0 2c-2.67 0-8 1.34-8 4v2h10v-2c0-1.64.84-3.09 2.17-4.13C11.19 12.34 9.62 13 8 13Zm8 0c-.54 0-1.06.05-1.55.14A6 6 0 0 1 20 19v2h4v-2c0-2.66-5.33-4-8-4Z"/></svg>
              <svg v-else-if="kpi.icon==='sparkles'" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="h-6 w-6 text-emerald-600"><path d="M9 12 7 7 2 5l5-2 2-5 2 5 5 2-5 2-2 5Zm10 12-1.5-4.5L13 18l4.5-1.5L19 12l1.5 4.5L25 18l-4.5 1.5L19 24Z"/></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-amber-600"><path d="M12 1a4 4 0 0 1 4 4v1h3a2 2 0 0 1 2 2v3H3V8a2 2 0 0 1 2-2h3V5a4 4 0 0 1 4-4Zm-9 12h18v6a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-6Z"/></svg>
            </div>
          </div>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-2">
        <!-- Recent Invoices -->
        <div class="overflow-hidden rounded-xl border bg-white shadow-sm dark:bg-zinc-900">
          <div class="flex items-center justify-between border-b p-4">
            <h2 class="text-lg font-semibold">Recent Invoices</h2>
          </div>
          <div class="divide-y">
            <div v-for="inv in props.recent_invoices" :key="inv.id" class="grid grid-cols-5 items-center gap-2 p-4 text-sm">
              <div class="col-span-2 font-medium">{{ inv.number }}</div>
              <div><span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', statusClasses(inv.status)]">{{ inv.status }}</span></div>
              <div class="text-muted-foreground">{{ inv.issued_at || '—' }}</div>
              <div class="text-right font-medium">{{ inv.currency }} {{ inv.total.toFixed(2) }}</div>
            </div>
            <div v-if="!props.recent_invoices?.length" class="p-6 text-center text-sm text-muted-foreground">No invoices yet.</div>
          </div>
        </div>

        <!-- Recent Signups -->
        <div class="overflow-hidden rounded-xl border bg-white shadow-sm dark:bg-zinc-900">
          <div class="flex items-center justify-between border-b p-4">
            <h2 class="text-lg font-semibold">Recent Signups</h2>
          </div>
          <div class="divide-y">
            <div v-for="u in props.recent_signups" :key="u.id" class="flex items-center gap-3 p-4">
              <div class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-indigo-500/20 to-emerald-500/20 text-sm font-semibold text-indigo-700 ring-1 ring-inset ring-black/5">
                {{ initials(u.name, u.email) }}
              </div>
              <div class="min-w-0 flex-1">
                <div class="truncate font-medium">{{ u.name || '—' }}</div>
                <div class="truncate text-sm text-muted-foreground">{{ u.email }}</div>
              </div>
              <div class="text-sm text-muted-foreground">{{ u.created_at || '—' }}</div>
            </div>
            <div v-if="!props.recent_signups?.length" class="p-6 text-center text-sm text-muted-foreground">No recent signups.</div>
          </div>
        </div>
      </div>

      <!-- Verifications (Last 7 days) -->
      <div class="overflow-hidden rounded-xl border bg-white shadow-sm dark:bg-zinc-900">
        <div class="flex items-center justify-between border-b p-4">
          <h2 class="text-lg font-semibold">Verifications · Last 7 days</h2>
        </div>
        <div class="p-6">
          <div class="flex items-end gap-3 h-48">
            <div v-for="b in chart.bars" :key="b.date" class="flex w-10 flex-col items-center gap-2">
              <div class="relative w-full rounded-t bg-indigo-500/80 transition hover:bg-indigo-500" :style="{ height: b.h + '%' }" :title="`${b.label}: ${b.count}`">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs text-muted-foreground">{{ b.count }}</div>
              </div>
              <div class="text-xs text-muted-foreground">{{ b.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
