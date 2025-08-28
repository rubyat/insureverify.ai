<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue'
import Swal from 'sweetalert2'
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

defineProps<{ plans: any }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Plans', href: '/admin/plans' },
];

// Delete single
const destroyForm = useForm({});
const destroyPlan = (id: number) => {
  destroyForm.delete(route('admin.plans.destroy', id), { preserveScroll: true })
}

const confirmDelete = async (id: number) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#dc2626',
  })
  if (result.isConfirmed) {
    destroyPlan(id)
  }
}

// Search
const search = ref('')
const performSearch = () => {
  router.get(route('admin.plans.index'), { search: search.value }, { preserveState: true, replace: true })
}

// Safely format pagination labels coming from the server.
// Strips any HTML tags and decodes HTML entities like &laquo; and &raquo;.
const formatLinkLabel = (label: string): string => {
  if (!label) return ''
  // Remove any HTML tags that could be present
  const stripped = label.replace(/<[^>]*>/g, '')
  // Decode HTML entities
  const textarea = document.createElement('textarea')
  textarea.innerHTML = stripped
  return textarea.value
}
</script>

<template>
  <Head title="Plans" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Plans</h1>
        <Link :href="route('admin.plans.create')" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Create Plan</Link>
      </div>

      <!-- Toolbar: search only -->
      <div class="flex flex-wrap items-center justify-end gap-2">
        <input v-model="search" type="text" placeholder="Search by name" class="rounded border px-3 py-2" />
        <button @click="performSearch" class="rounded bg-primary px-4 py-2 text-white">Search</button>
      </div>

      <div class="overflow-x-auto rounded-md border bg-white">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Slug</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="plan in plans.data" :key="plan.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <Link :href="route('admin.plans.edit', plan.id)" class="text-primary hover:underline">{{ plan.name }}</Link>
              </td>
              <td class="px-4 py-3">{{ plan.slug }}</td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', plan.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700']">
                  {{ plan.is_active ? 'Publish' : 'Draft' }}
                </span>
              </td>
              <td class="px-4 py-3">{{ plan.created_at ? new Date(plan.created_at).toLocaleDateString() : '' }}</td>
              <td class="px-4 py-3 text-right space-x-2">
                <div class="">
                  <Link :href="route('admin.plans.edit', plan.id)" class="inline-flex items-center gap-2 rounded bg-primary px-3 py-1.5 text-sm text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M15.586 3.586a2 2 0 0 1 2.828 2.828l-9.193 9.193a4 4 0 0 1-1.414.94l-3.122 1.104a.5.5 0 0 1-.632-.632l1.104-3.122a4 4 0 0 1 .94-1.414l9.193-9.193ZM12 5l3 3"/></svg>
                  </Link>
                  <button @click="confirmDelete(plan.id)" class="ml-2 inline-flex items-center gap-2 rounded border border-red-200 bg-red-50 px-3 py-1.5 text-sm text-red-700 hover:bg-red-100 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-1 1v1H5.5a1 1 0 1 0 0 2H6v13a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V6h.5a1 1 0 1 0 0-2H16V3a1 1 0 0 0-1-1H9Zm2 5a1 1 0 0 0-1 1v9a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Zm4 0a1 1 0 0 0-1 1v9a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Z" clip-rule="evenodd"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="plans.links">
        <Link
          v-for="link in plans.links"
          :key="link.url + link.label"
          :href="link.url || '#'"
          :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]"
        >
          {{ formatLinkLabel(link.label) }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
