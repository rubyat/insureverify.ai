<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import BlogForm from './BlogForm.vue'

const props = defineProps<{ categories: any[] }>()
</script>

<template>
  <Head title="Create Blog" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/admin/dashboard' }, { title: 'Blogs', href: route('admin.blogs.index') }, { title: 'Create', href: '#' }]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Create Blog</h1>
      </div>

      <BlogForm :categories="props.categories" />
    </div>
  </AppLayout>
</template>
