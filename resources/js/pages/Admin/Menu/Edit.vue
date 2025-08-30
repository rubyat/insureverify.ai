<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import MenuForm from './MenuForm.vue'

const props = defineProps<{ menu: any }>()
</script>

<template>
  <Head :title="`Edit Menu: ${props.menu?.name || ''}`" />
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Menus', href: route('admin.menu.index') },
    { title: props.menu?.name || 'Edit', href: route('admin.menu.edit', props.menu?.id) }
  ]">
    <div class="p-6 space-y-6 bg-gray-50">
      <h1 class="text-xl font-semibold">Edit Menu</h1>
      <div class="rounded border bg-white p-4">
        <MenuForm :menu="props.menu" :submit-route="route('admin.menu.update', props.menu.id)" method="put" />
      </div>
    </div>
  </AppLayout>
</template>
