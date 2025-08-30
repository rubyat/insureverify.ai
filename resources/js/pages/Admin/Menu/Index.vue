<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { decodeAndStrip } from '@/utils/strings'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{ menus: any, filters?: any }>()

const search = ref(props.filters?.q || '')
const performSearch = () => {
  router.get(route('admin.menu.index'), { q: search.value }, { preserveState: true, replace: true })
}
</script>

<template>
  <Head title="Menus" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/admin/dashboard' }, { title: 'Menus', href: route('admin.menu.index') }]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Menus</h1>
        <Link :href="route('admin.menu.create')" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Add new menu</Link>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-2">
        <input v-model="search" type="text" placeholder="Search by name" class="rounded border px-3 py-2" />
        <button @click="performSearch" class="rounded bg-primary px-4 py-2 text-white">Search</button>
      </div>

      <div class="overflow-x-auto rounded-md border bg-white">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2"></th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Updated</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="m in menus.data" :key="m.id" class="hover:bg-gray-50">
              <td class="px-4 py-3"><input type="checkbox" /></td>
              <td class="px-4 py-3">
                <Link :href="route('admin.menu.edit', m.id)" class="text-primary hover:underline">{{ m.name }}</Link>
              </td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', m.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700']">
                  {{ m.status === 'active' ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-4 py-3">{{ new Date(m.updated_at).toLocaleString() }}</td>
              <td class="px-4 py-3 text-right space-x-2">
                <Link :href="route('admin.menu.edit', m.id)" class="inline-flex items-center gap-2 rounded bg-primary px-3 py-1.5 text-sm text-white">Edit</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="menus.links">
        <Link v-for="link in menus.links" :key="(link.url || '') + link.label" :href="link.url || '#'" :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]">{{ decodeAndStrip(link.label) }}</Link>
      </div>
    </div>
  </AppLayout>
</template>
