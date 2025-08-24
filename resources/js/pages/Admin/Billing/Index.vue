<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{
  invoices: any,
  metrics: Record<string, number>,
  filters: { status?: string | null, user_id?: number | null }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Billing', href: '/admin/billing' },
]

const status = ref(props.filters?.status || '')
const userId = ref(props.filters?.user_id || '')
const applyFilters = () => {
  router.get(route('admin.billing.index'), { status: status.value || undefined, user_id: userId.value || undefined }, { preserveState: true, replace: true })
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
  <Head title="Billing" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Invoices</h1>
        <div class="text-sm text-gray-600">Total: {{ metrics.count }} · Paid: {{ metrics.paid }} · Open: {{ metrics.open }}</div>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-2">
        <select v-model="status" class="rounded border px-3 py-2">
          <option value="">All statuses</option>
          <option value="paid">Paid</option>
          <option value="open">Open</option>
          <option value="void">Void</option>
        </select>
        <input v-model="userId" type="number" min="1" placeholder="User ID" class="rounded border px-3 py-2 w-28" />
        <button @click="applyFilters" class="rounded bg-primary px-4 py-2 text-white">Apply</button>
      </div>

      <div class="overflow-x-auto rounded-md border bg-white">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">Number</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">User</th>
              <th class="px-4 py-2">Total</th>
              <th class="px-4 py-2">Issued</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">{{ inv.number }}</td>
              <td class="px-4 py-3">{{ inv.status }}</td>
              <td class="px-4 py-3">{{ inv.user?.name }} <span class="text-gray-500">{{ inv.user?.email }}</span></td>
              <td class="px-4 py-3">${{ inv.total.toFixed(2) }}</td>
              <td class="px-4 py-3">{{ inv.issued_at }}</td>
              <td class="px-4 py-3 text-right">
                <Link :href="route('admin.billing.show', inv.id)" class="inline-flex items-center gap-2 rounded bg-primary px-3 py-1.5 text-sm text-white">View</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="invoices.links">
        <Link v-for="link in invoices.links" :key="link.url + link.label" :href="link.url || '#'" :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]"><span>{{ formatLinkLabel(link.label) }}</span></Link>
      </div>
    </div>
  </AppLayout>
</template>
