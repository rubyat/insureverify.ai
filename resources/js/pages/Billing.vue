<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{ subscription: { status?: string | null; plan_name?: string | null; price?: string | number | null }; portalUrl: string }>();
</script>

<template>
  <Head title="Billing" />
  <AppLayout :breadcrumbs="[{ title: 'Billing', href: '/billing' }]">
    <div class="p-6 space-y-6 max-w-3xl">
      <h1 class="text-2xl font-semibold">Billing</h1>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Current Subscription</h2>
        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
          <div>
            <div class="text-sm text-muted-foreground">Plan</div>
            <div class="font-medium">{{ props.subscription.plan_name ?? '—' }}</div>
          </div>
          <div>
            <div class="text-sm text-muted-foreground">Price</div>
            <div class="font-medium">{{ props.subscription.price ? `$${Number(props.subscription.price).toFixed(2)}/mo` : '—' }}</div>
          </div>
          <div>
            <div class="text-sm text-muted-foreground">Status</div>
            <div class="font-medium">{{ props.subscription.status ?? '—' }}</div>
          </div>
        </div>
      </div>

      <div>
        <a :href="props.portalUrl" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Manage Billing & Invoices</a>
      </div>
    </div>
  </AppLayout>
</template>


