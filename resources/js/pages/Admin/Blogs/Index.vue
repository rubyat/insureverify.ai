<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { decodeAndStrip } from '@/utils/strings'
import Swal from 'sweetalert2'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{ blogs: any, filters?: any }>()

const search = ref(props.filters?.q || '')
const performSearch = () => {
  router.get(route('admin.blogs.index'), { q: search.value }, { preserveState: true, replace: true })
}

const confirmClone = async (id: number) => {
  const result = await Swal.fire({
    title: 'Clone this blog?',
    text: 'A copy will be created with status set to Draft.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Clone',
    cancelButtonText: 'Cancel',
  })
  if (result.isConfirmed) {
    router.post(route('admin.blogs.clone', id), {}, { preserveScroll: true })
  }
}

const confirmDelete = async (id: number) => {
  const result = await Swal.fire({
    title: 'Delete this blog?',
    text: 'This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel',
  })
  if (result.isConfirmed) {
    router.delete(route('admin.blogs.destroy', id), { preserveScroll: true })
  }
}

const openActionId = ref<number | null>(null)
const toggleActions = (id: number) => {
  openActionId.value = openActionId.value === id ? null : id
}
</script>

<template>
  <Head title="Blogs" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/admin/dashboard' }, { title: 'Blogs', href: route('admin.blogs.index') }]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Blogs</h1>
        <Link :href="route('admin.blogs.create')" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Add new blog</Link>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-2">
        <input v-model="search" type="text" placeholder="Search by name" class="rounded border px-3 py-2" />
        <button @click="performSearch" class="rounded bg-primary px-4 py-2 text-white">Search</button>
      </div>

      <div class="overflow-x-auto rounded-md border bg-white">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">Title</th>
              <th class="px-4 py-2">Slug</th>
              <th class="px-4 py-2">Category</th>
              <th class="px-4 py-2">Publish Date</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="b in blogs.data" :key="b.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <Link :href="route('admin.blogs.edit', b.id)" class="text-primary hover:underline">{{ b.title }}</Link>
              </td>
              <td class="px-4 py-3">/{{ b.slug }}</td>
              <td class="px-4 py-3">{{ b.category?.title || '-' }}</td>
              <td class="px-4 py-3">{{ b.publish_date ? new Date(b.publish_date).toLocaleDateString() : '' }}</td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', b.status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700']">
                  {{ b.status ? 'Publish' : 'Draft' }}
                </span>
              </td>
                <td class="px-4 py-3 text-right space-x-2 relative">
                <Link :href="route('admin.blogs.builder', b.id)" class="inline-flex items-center gap-2 rounded bg-slate-700 px-3 py-1.5 text-sm text-white">Template Builder</Link>

                <button type="button" class="inline-flex items-center gap-2 rounded border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50"
                        @click="toggleActions(b.id)">
                  Action ▾
                </button>

                <div v-if="openActionId === b.id" class="absolute right-4 mt-2 w-40 rounded border bg-white shadow z-10 text-left">
                  <ul class="py-1 text-sm">
                    <li>
                      <Link :href="route('admin.blogs.edit', b.id)" class="block w-full px-3 py-2 hover:bg-gray-100 text-left">Edit</Link>
                    </li>
                    <li>
                      <button @click="confirmClone(b.id)" class="block w-full px-3 py-2 hover:bg-gray-100 text-left">Clone</button>
                    </li>
                    <li>
                      <button @click="confirmDelete(b.id)" class="block w-full px-3 py-2 hover:bg-red-50 text-red-600 text-left">Delete</button>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="blogs.links">
        <Link v-for="link in blogs.links" :key="link.url + link.label" :href="link.url || '#'" :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]">
          {{ decodeAndStrip(link.label) }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
