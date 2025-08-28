<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
  metrics: { activeSubscriptions: number };
  invoices: Array<{ id: string; customer_email: string; total: number; status: string; created: number }>;
  subscribedUsers: Array<{ id: number; name: string; email: string; plan?: string | null }>;
}>();
</script>

<template>
  <Head title="Reports" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/admin/dashboard' }, { title: 'Reports', href: '/admin/reports' }]"></AppLayout>
  <div class="p-6 space-y-8">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div class="rounded border p-4">
        <div class="text-sm text-muted-foreground">Active Subscriptions</div>
        <div class="mt-2 text-2xl font-semibold">{{ metrics.activeSubscriptions }}</div>
      </div>
    </div>

    <div class="space-y-3">
      <h2 class="text-lg font-semibold">Recent Invoices</h2>
      <div class="overflow-x-auto rounded border">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Customer</th>
              <th class="px-4 py-2">Total</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Created</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="i in invoices" :key="i.id">
              <td class="px-4 py-2">{{ i.id }}</td>
              <td class="px-4 py-2">{{ i.customer_email }}</td>
              <td class="px-4 py-2">${{ i.total.toFixed(2) }}</td>
              <td class="px-4 py-2">{{ i.status }}</td>
              <td class="px-4 py-2">{{ new Date(i.created * 1000).toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="space-y-3">
      <h2 class="text-lg font-semibold">Subscribed Users</h2>
      <div class="overflow-x-auto rounded border">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Plan</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="u in subscribedUsers" :key="u.id">
              <td class="px-4 py-2">{{ u.name }}</td>
              <td class="px-4 py-2">{{ u.email }}</td>
              <td class="px-4 py-2">{{ u.plan ?? '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

