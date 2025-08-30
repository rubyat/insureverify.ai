<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import CategoryForm from './CategoryForm.vue'
</script>

<template>
  <Head title="Create Blog Category" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/admin/dashboard' }, { title: 'Blog Categories', href: route('admin.blog-categories.index') }, { title: 'Create', href: '#' }]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Create Blog Category</h1>
      </div>

      <CategoryForm />
    </div>
  </AppLayout>
</template>
