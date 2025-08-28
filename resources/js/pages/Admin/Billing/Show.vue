<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{ invoice: any }>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Billing', href: '/admin/billing' },
  { title: props.invoice?.number || 'Invoice', href: '#' },
]
</script>

<template>
  <Head :title="`Invoice ${invoice?.number || ''}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-xl font-semibold">Invoice {{ invoice.number }}</h1>
          <div class="text-gray-600 text-sm">Status: {{ invoice.status }} · Period: {{ invoice.period.start }} → {{ invoice.period.end }}</div>
        </div>
        <Link :href="route('admin.billing.index')" class="rounded border px-3 py-1.5">Back</Link>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="rounded border bg-white p-4">
          <h2 class="mb-3 text-base font-semibold">Summary</h2>
          <div class="space-y-1 text-sm">
            <div><span class="text-gray-500">Currency:</span> {{ invoice.currency }}</div>
            <div><span class="text-gray-500">Subtotal:</span> ${{ invoice.subtotal.toFixed(2) }}</div>
            <div><span class="text-gray-500">Tax:</span> ${{ invoice.tax.toFixed(2) }}</div>
            <div class="font-medium"><span class="text-gray-500">Total:</span> ${{ invoice.total.toFixed(2) }}</div>
            <div><span class="text-gray-500">Issued:</span> {{ invoice.issued_at }}</div>
            <div v-if="invoice.user"><span class="text-gray-500">User:</span> {{ invoice.user.name }} ({{ invoice.user.email }})</div>
          </div>
        </div>

        <div class="rounded border bg-white p-4">
          <h2 class="mb-3 text-base font-semibold">Payments</h2>
          <div class="overflow-x-auto rounded-md border bg-white">
            <table class="min-w-full divide-y">
              <thead>
                <tr class="text-left">
                  <th class="px-4 py-2">Status</th>
                  <th class="px-4 py-2">Provider</th>
                  <th class="px-4 py-2">Amount</th>
                  <th class="px-4 py-2">Paid At</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr v-for="p in invoice.payments" :key="p.paid_at + p.amount" class="hover:bg-gray-50">
                  <td class="px-4 py-3">{{ p.status }}</td>
                  <td class="px-4 py-3">{{ p.provider }}</td>
                  <td class="px-4 py-3">${{ p.amount.toFixed(2) }}</td>
                  <td class="px-4 py-3">{{ p.paid_at }}</td>
                </tr>
                <tr v-if="!invoice.payments || invoice.payments.length === 0"><td colspan="4" class="px-4 py-6 text-center text-gray-500">No payments</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="rounded border bg-white p-4">
        <h2 class="mb-3 text-base font-semibold">Items</h2>
        <div class="overflow-x-auto rounded-md border bg-white">
          <table class="min-w-full divide-y">
            <thead>
              <tr class="text-left">
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Qty</th>
                <th class="px-4 py-2">Unit</th>
                <th class="px-4 py-2">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="it in invoice.items" :key="it.description + it.amount" class="hover:bg-gray-50">
                <td class="px-4 py-3">{{ it.type }}</td>
                <td class="px-4 py-3">{{ it.description }}</td>
                <td class="px-4 py-3">{{ it.qty }}</td>
                <td class="px-4 py-3">${{ it.unit.toFixed(2) }}</td>
                <td class="px-4 py-3">${{ it.amount.toFixed(2) }}</td>
              </tr>
              <tr v-if="!invoice.items || invoice.items.length === 0"><td colspan="5" class="px-4 py-6 text-center text-gray-500">No items</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
