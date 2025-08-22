<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ users: Array<{ id: number; name: string; email: string; roles: string[]; plan?: string | null; status?: string | null }> }>();
</script>

<template>
  <Head title="Users" />
  <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/admin' }, { title: 'Users', href: '/admin/users' }]"></AppLayout>
  <div class="p-6">
    <div class="overflow-x-auto rounded border">
      <table class="min-w-full divide-y">
        <thead>
          <tr class="text-left">
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Roles</th>
            <th class="px-4 py-2">Plan</th>
            <th class="px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="u in users" :key="u.id">
            <td class="px-4 py-2">{{ u.name }}</td>
            <td class="px-4 py-2">{{ u.email }}</td>
            <td class="px-4 py-2">{{ u.roles.join(', ') }}</td>
            <td class="px-4 py-2">{{ u.plan ?? '—' }}</td>
            <td class="px-4 py-2">{{ u.status ?? '—' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

