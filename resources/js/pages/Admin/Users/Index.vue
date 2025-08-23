<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
  users: Array<{ id: number; name: string; email: string; roles: string[]; plan?: string | null; status?: string | null }>;
  canManageAdmins: boolean;
}>();

const makeAdminForm = useForm({});
const removeAdminForm = useForm({});

const isAdmin = (roles: string[]) => roles.includes('admin');
const promote = (id: number) => {
  makeAdminForm.post(route('admin.users.makeAdmin', id), {
    preserveScroll: true,
    onSuccess: () => router.reload({ only: ['users'] }),
  });
};
const demote = (id: number) => {
  removeAdminForm.post(route('admin.users.removeAdmin', id), {
    preserveScroll: true,
    onSuccess: () => router.reload({ only: ['users'] }),
  });
};
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
            <th class="px-4 py-2" v-if="props.canManageAdmins">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="u in users" :key="u.id">
            <td class="px-4 py-2">{{ u.name }}</td>
            <td class="px-4 py-2">{{ u.email }}</td>
            <td class="px-4 py-2">{{ u.roles.join(', ') }}</td>
            <td class="px-4 py-2">{{ u.plan ?? '—' }}</td>
            <td class="px-4 py-2">{{ u.status ?? '—' }}</td>
            <td class="px-4 py-2 space-x-2" v-if="props.canManageAdmins">
              <button v-if="!isAdmin(u.roles)" class="text-primary" @click="promote(u.id)">Promote to Admin</button>
              <button v-else class="text-red-600" @click="demote(u.id)">Remove Admin</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

