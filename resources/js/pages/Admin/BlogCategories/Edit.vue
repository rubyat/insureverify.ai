<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import CategoryForm from './CategoryForm.vue'

const props = defineProps<{ category: any }>()
</script>

<template>
  <Head :title="`Edit: ${props.category.title}`" />
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Blog Categories', href: route('admin.blog-categories.index') },
    { title: `Edit: ${props.category.title}`, href: '#' },
  ]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Edit: {{ props.category.title }}</h1>
      </div>

      <CategoryForm :category="props.category" />
    </div>
  </AppLayout>
</template>
