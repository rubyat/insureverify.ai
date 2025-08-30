<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import MenuForm from './MenuForm.vue'
</script>

<template>
  <Head title="Create Menu" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/admin/dashboard' }, { title: 'Menus', href: route('admin.menu.index') }, { title: 'Create', href: route('admin.menu.create') }]">
    <div class="p-6 space-y-6 bg-gray-50">
      <h1 class="text-xl font-semibold">Create Menu</h1>
      <div class="rounded border bg-white p-4">
        <MenuForm :submit-route="route('admin.menu.store')" method="post" />
      </div>
    </div>
  </AppLayout>
</template>
