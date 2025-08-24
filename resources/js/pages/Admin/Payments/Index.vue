<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{
  payments: any,
  metrics: Record<string, number>,
  filters: { status?: string | null }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Payments', href: '/admin/payments' },
]

const status = ref(props.filters?.status || '')
const applyFilters = () => {
  router.get(route('admin.payments.index'), { status: status.value || undefined }, { preserveState: true, replace: true })
}

const formatLinkLabel = (label: string): string => {
  if (!label) return ''
  const stripped = label.replace(/<[^>]*>/g, '')
  const textarea = document.createElement('textarea')
  textarea.innerHTML = stripped
  return textarea.value
}
</script>

<template>
  <Head title="Payments" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Payments</h1>
        <div class="text-sm text-gray-600">Total: {{ metrics.count }} · Succeeded: {{ metrics.succeeded }} · Failed: {{ metrics.failed }}</div>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-2">
        <select v-model="status" class="rounded border px-3 py-2">
          <option value="">All statuses</option>
          <option value="succeeded">Succeeded</option>
          <option value="failed">Failed</option>
        </select>
        <button @click="applyFilters" class="rounded bg-primary px-4 py-2 text-white">Apply</button>
      </div>

      <div class="overflow-x-auto rounded-md border bg-white">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Amount</th>
              <th class="px-4 py-2">Currency</th>
              <th class="px-4 py-2">Paid At</th>
              <th class="px-4 py-2">User</th>
              <th class="px-4 py-2">Invoice</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="p in payments.data" :key="p.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">{{ p.status }}</td>
              <td class="px-4 py-3">${{ p.amount.toFixed(2) }}</td>
              <td class="px-4 py-3">{{ p.currency }}</td>
              <td class="px-4 py-3">{{ p.paid_at }}</td>
              <td class="px-4 py-3">{{ p.user?.name }} <span class="text-gray-500">{{ p.user?.email }}</span></td>
              <td class="px-4 py-3">{{ p.invoice?.number }} ({{ p.invoice?.status }})</td>
              <td class="px-4 py-3 text-right">
                <Link :href="route('admin.payments.show', p.id)" class="inline-flex items-center gap-2 rounded bg-primary px-3 py-1.5 text-sm text-white">View</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="payments.links">
        <Link v-for="link in payments.links" :key="link.url + link.label" :href="link.url || '#'" :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]"><span>{{ formatLinkLabel(link.label) }}</span></Link>
      </div>
    </div>
  </AppLayout>
</template>
