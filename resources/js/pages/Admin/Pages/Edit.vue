<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import PageForm from './PageForm.vue'

const props = defineProps<{ page: any }>()
</script>

<template>
  <Head :title="`Edit: ${props.page.title}`" />
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Pages', href: route('admin.pages.index') },
    { title: `Edit: ${props.page.title}`, href: '#' },
  ]">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Edit: {{ props.page.title }}</h1>
        <Link :href="route('admin.pages.builder', props.page.id)" class="inline-flex items-center rounded-md bg-slate-700 px-4 py-2 text-white">Template Builder</Link>
      </div>

      <PageForm :page="props.page" />
    </div>
  </AppLayout>
</template>
