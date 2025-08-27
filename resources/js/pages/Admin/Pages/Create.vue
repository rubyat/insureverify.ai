<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import PageForm from './PageForm.vue'

</script>

<template>
  <Head title="Create Page" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/dashboard' }, { title: 'Pages', href: route('admin.pages.index') }, { title: 'Create', href: '#' }]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Create Page</h1>
      </div>

      <PageForm />
    </div>
  </AppLayout>
</template>
