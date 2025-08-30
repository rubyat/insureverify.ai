<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { decodeAndStrip } from '@/utils/strings'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{ categories: any, filters?: any }>()

const search = ref(props.filters?.q || '')
const performSearch = () => {
  router.get(route('admin.blog-categories.index'), { q: search.value }, { preserveState: true, replace: true })
}
</script>

<template>
  <Head title="Blog Categories" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/admin/dashboard' }, { title: 'Blog Categories', href: route('admin.blog-categories.index') }]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Blog Categories</h1>
        <Link :href="route('admin.blog-categories.create')" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Add new category</Link>
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
            <tr v-for="c in categories.data" :key="c.id" class="hover:bg-gray-50">
              <td class="px-4 py-3"><input type="checkbox" /></td>
              <td class="px-4 py-3">
                <Link :href="route('admin.blog-categories.edit', c.id)" class="text-primary hover:underline">{{ c.title }}</Link>
              </td>
              <td class="px-4 py-3">/{{ c.slug }}</td>
              <td class="px-4 py-3">{{ c.updated_at ? new Date(c.updated_at).toLocaleDateString() : '' }}</td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', c.status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700']">
                  {{ c.status ? 'Publish' : 'Draft' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <Link :href="route('admin.blog-categories.edit', c.id)" class="inline-flex items-center gap-2 rounded bg-primary px-3 py-1.5 text-sm text-white">Edit</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="categories.links">
        <Link v-for="link in categories.links" :key="link.url + link.label" :href="link.url || '#'" :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]">
          {{ decodeAndStrip(link.label) }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
