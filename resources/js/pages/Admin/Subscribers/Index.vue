<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { decodeAndStrip } from '@/utils/strings'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{
  items: any,
  metrics: Record<string, number>,
  filters: { search?: string | null }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Subscribers', href: '/admin/subscribers' },
]

const search = ref(props.filters?.search || '')
const performSearch = () => {
  router.get(route('admin.subscribers.index'), { search: search.value }, { preserveState: true, replace: true })
}

// decodeAndStrip is used for pagination labels

const totalCount = computed(() => props.items?.total || 0)
</script>

<template>
  <Head title="Subscribers" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Subscribers</h1>
        <div class="text-sm text-gray-600">Total: {{ totalCount }}, Active: {{ metrics.active }}, Trialing: {{ metrics.trialing }}, Past due: {{ metrics.past_due }}</div>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-2">
        <input v-model="search" type="text" placeholder="Search by name or email" class="rounded border px-3 py-2" />
        <button @click="performSearch" class="rounded bg-primary px-4 py-2 text-white">Search</button>
      </div>

      <div class="overflow-x-auto rounded-md border bg-white">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Plan</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Period End</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="u in items.data" :key="u.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <Link :href="route('admin.subscribers.show', u.id)" class="text-primary hover:underline">{{ u.name }}</Link>
              </td>
              <td class="px-4 py-3">{{ u.email }}</td>
              <td class="px-4 py-3">{{ u.plan || '-' }}</td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                  u.status === 'active' ? 'bg-green-100 text-green-700' :
                  u.status === 'trialing' ? 'bg-blue-100 text-blue-700' :
                  u.status === 'past_due' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700']">
                  {{ u.status || 'none' }}
                </span>
              </td>
              <td class="px-4 py-3">{{ u.periodEnd }}</td>
              <td class="px-4 py-3 text-right">
                <Link :href="route('admin.subscribers.show', u.id)" class="inline-flex items-center gap-2 rounded bg-primary px-3 py-1.5 text-sm text-white">View</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="items.links">
        <Link
          v-for="link in items.links"
          :key="link.url + link.label"
          :href="link.url || '#'"
          :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]"
        >
          {{ decodeAndStrip(link.label) }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
