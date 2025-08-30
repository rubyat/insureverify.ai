<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { decodeAndStrip } from '@/utils/strings'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{ pages: any, filters?: any }>()

const search = ref(props.filters?.q || '')
const performSearch = () => {
  router.get(route('admin.pages.index'), { q: search.value }, { preserveState: true, replace: true })
}
</script>

<template>
  <Head title="Pages" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/admin/dashboard' }, { title: 'Pages', href: route('admin.pages.index') }]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Pages</h1>
        <Link :href="route('admin.pages.create')" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Add new page</Link>
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
              <th class="px-4 py-2">Title</th>
              <th class="px-4 py-2">Slug</th>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="p in pages.data" :key="p.id" class="hover:bg-gray-50">
              <td class="px-4 py-3"><input type="checkbox" /></td>
              <td class="px-4 py-3">
                <Link :href="route('admin.pages.edit', p.id)" class="text-primary hover:underline">{{ p.title }}</Link>
              </td>
              <td class="px-4 py-3">/{{ p.slug }}</td>
              <td class="px-4 py-3">{{ p.updated_at ? new Date(p.updated_at).toLocaleDateString() : '' }}</td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', p.status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700']">
                  {{ p.status ? 'Publish' : 'Draft' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <Link :href="route('admin.pages.builder', p.id)" class="inline-flex items-center gap-2 rounded bg-slate-700 px-3 py-1.5 text-sm text-white">Template Builder</Link>
                <Link :href="route('admin.pages.edit', p.id)" class="inline-flex items-center gap-2 rounded bg-primary px-3 py-1.5 text-sm text-white">Edit</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="pages.links">
        <Link v-for="link in pages.links" :key="link.url + link.label" :href="link.url || '#'" :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]">
          {{ decodeAndStrip(link.label) }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
