<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{
  user: { id: number; name: string; email: string },
  subscription: any | null,
  invoices: any[] | undefined,
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Subscribers', href: '/admin/subscribers' },
  { title: props.user?.name || 'Subscriber', href: '#' },
]
</script>

<template>
  <Head :title="`Subscriber - ${user?.name || ''}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-xl font-semibold">{{ user.name }}</h1>
          <p class="text-gray-600">{{ user.email }}</p>
        </div>
        <Link :href="route('admin.subscribers.index')" class="rounded border px-3 py-1.5">Back</Link>
      </div>

      <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded border bg-white p-4">
          <h2 class="mb-3 text-base font-semibold">Subscription</h2>
          <div v-if="subscription" class="space-y-1 text-sm">
            <div><span class="text-gray-500">Status:</span> {{ subscription.status }}</div>
            <div><span class="text-gray-500">Plan:</span> {{ subscription.plan }}</div>
            <div><span class="text-gray-500">Period:</span> {{ subscription.period.start }} → {{ subscription.period.end }}</div>
            <div><span class="text-gray-500">Included:</span> {{ subscription.included }}</div>
            <div><span class="text-gray-500">Used:</span> {{ subscription.used }}</div>
            <div><span class="text-gray-500">Price (monthly):</span> ${{ subscription.priceMonthly.toFixed(2) }}</div>
          </div>
          <div v-else class="text-sm text-gray-600">No active subscription.</div>
        </div>

        <div class="rounded border bg-white p-4">
          <h2 class="mb-3 text-base font-semibold">Recent Invoices</h2>
          <div class="overflow-x-auto rounded-md border bg-white">
            <table class="min-w-full divide-y">
              <thead>
                <tr class="text-left">
                  <th class="px-4 py-2">Number</th>
                  <th class="px-4 py-2">Status</th>
                  <th class="px-4 py-2">Total</th>
                  <th class="px-4 py-2">Issued</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr v-for="inv in invoices" :key="inv.number" class="hover:bg-gray-50">
                  <td class="px-4 py-3">{{ inv.number }}</td>
                  <td class="px-4 py-3">{{ inv.status }}</td>
                  <td class="px-4 py-3">${{ inv.total.toFixed(2) }}</td>
                  <td class="px-4 py-3">{{ inv.issued_at }}</td>
                </tr>
                <tr v-if="!invoices || invoices.length === 0"><td colspan="4" class="px-4 py-6 text-center text-gray-500">No invoices</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
