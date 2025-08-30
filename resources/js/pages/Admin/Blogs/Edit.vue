<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import BlogForm from './BlogForm.vue'

const props = defineProps<{ blog: any, categories: any[] }>()
</script>

<template>
  <Head :title="`Edit: ${props.blog.title}`" />
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Blogs', href: route('admin.blogs.index') },
    { title: `Edit: ${props.blog.title}`, href: '#' },
  ]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Edit: {{ props.blog.title }}</h1>
        <Link :href="route('admin.blogs.builder', props.blog.id)" class="inline-flex items-center rounded-md bg-slate-700 px-4 py-2 text-white">Template Builder</Link>
      </div>

      <BlogForm :blog="props.blog" :categories="props.categories" />
    </div>
  </AppLayout>
</template>
