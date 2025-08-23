<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
  currentPlan: { name: string; price: number; renewalDate: string };
  invoices: Array<{ id: string; date: string; amount: number; url: string }>;
  paymentMethod: { brand: string; last4: string; exp: string };
  errors: string[];
}>();

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Billing', href: '/app/billing' }];
</script>

<template>
  <Head title="Billing" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-6 max-w-3xl">
      <h1 class="text-2xl font-semibold">Billing</h1>

      <div v-if="errors?.length" class="space-y-2">
        <div v-for="(e, i) in errors" :key="i" class="rounded border border-red-300 bg-red-50 p-3 text-sm text-red-900">{{ e }}</div>
      </div>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Current Plan</h2>
        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-3">
          <div>
            <div class="text-sm text-muted-foreground">Plan</div>
            <div class="font-medium">{{ currentPlan.name }}</div>
          </div>
          <div>
            <div class="text-sm text-muted-foreground">Price</div>
            <div class="font-medium">${{ currentPlan.price.toFixed(2) }}/mo</div>
          </div>
          <div>
            <div class="text-sm text-muted-foreground">Renews</div>
            <div class="font-medium">{{ currentPlan.renewalDate }}</div>
          </div>
        </div>
      </div>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Payment Method</h2>
        <div class="mt-2 text-sm">{{ paymentMethod.brand }} ending •••• {{ paymentMethod.last4 }} (exp {{ paymentMethod.exp }})</div>
        <button class="mt-3 inline-flex rounded-md border px-3 py-2 text-sm">Update payment method</button>
      </div>

      <div class="rounded border">
        <div class="border-b p-3 font-medium">Invoices</div>
        <div class="divide-y">
          <div v-for="inv in invoices" :key="inv.id" class="flex items-center justify-between p-3 text-sm">
            <div>{{ inv.id }}</div>
            <div class="text-muted-foreground">{{ inv.date }}</div>
            <div>${{ inv.amount.toFixed(2) }}</div>
            <a :href="inv.url" class="rounded border px-2 py-1">PDF</a>
          </div>
        </div>
      </div>
      </div>
    </CustomerLayout>
  </component>
</template>
