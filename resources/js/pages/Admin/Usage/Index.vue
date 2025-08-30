<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { decodeAndStrip } from '@/utils/strings'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{
  usages: any,
  metrics: Record<string, number>,
  filters: { subscription_id?: number | null, user_id?: number | null, metric?: string | null }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Usage', href: '/admin/usage' },
]

const subscriptionId = ref(props.filters?.subscription_id || '')
const userId = ref(props.filters?.user_id || '')
const metric = ref(props.filters?.metric || 'verifications')
const applyFilters = () => {
  router.get(route('admin.usage.index'), {
    subscription_id: subscriptionId.value || undefined,
    user_id: userId.value || undefined,
    metric: metric.value || undefined,
  }, { preserveState: true, replace: true })
}

// Use shared util for pagination label decoding
</script>

<template>
  <Head title="Usage" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Usage</h1>
        <div class="text-sm text-gray-600">Rows: {{ metrics.rows }}</div>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-2">
        <input v-model="subscriptionId" type="number" min="1" placeholder="Subscription ID" class="rounded border px-3 py-2 w-40" />
        <input v-model="userId" type="number" min="1" placeholder="User ID" class="rounded border px-3 py-2 w-32" />
        <input v-model="metric" type="text" placeholder="Metric (e.g., verifications)" class="rounded border px-3 py-2 w-64" />
        <button @click="applyFilters" class="rounded bg-primary px-4 py-2 text-white">Apply</button>
      </div>

      <div class="overflow-x-auto rounded-md border bg-white">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">Metric</th>
              <th class="px-4 py-2">Used</th>
              <th class="px-4 py-2">Period</th>
              <th class="px-4 py-2">Subscription</th>
              <th class="px-4 py-2">User</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="row in usages.data" :key="row.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">{{ row.metric }}</td>
              <td class="px-4 py-3">{{ row.used }}</td>
              <td class="px-4 py-3">{{ row.period.start }} → {{ row.period.end }}</td>
              <td class="px-4 py-3">#{{ row.subscription?.id }} ({{ row.subscription?.status }})</td>
              <td class="px-4 py-3">{{ row.user?.name }} <span class="text-gray-500">{{ row.user?.email }}</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="usages.links">
        <Link v-for="link in usages.links" :key="link.url + link.label" :href="link.url || '#'" :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]">{{ decodeAndStrip(link.label) }}</Link>
      </div>
    </div>
  </AppLayout>
</template>
