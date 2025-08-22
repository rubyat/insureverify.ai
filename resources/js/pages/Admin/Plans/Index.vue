<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

defineProps<{ plans: any }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Plans', href: '/admin/plans' },
];

const destroyForm = useForm({});
const destroyPlan = (id: number) => {
  if (!confirm('Delete this plan?')) return;
  destroyForm.delete(route('admin.plans.destroy', id));
};
</script>

<template>
  <Head title="Plans" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Plans</h1>
        <Link :href="route('admin.plans.create')" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Create Plan</Link>
      </div>

      <div class="overflow-x-auto rounded-md border">
        <table class="min-w-full divide-y">
          <thead>
            <tr class="text-left">
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Slug</th>
              <th class="px-4 py-2">Stripe ID</th>
              <th class="px-4 py-2">Price</th>
              <th class="px-4 py-2">Image Limit</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="plan in plans.data" :key="plan.id">
              <td class="px-4 py-2">{{ plan.name }}</td>
              <td class="px-4 py-2">{{ plan.slug }}</td>
              <td class="px-4 py-2">{{ plan.stripe_plan_id }}</td>
              <td class="px-4 py-2">${{ Number(plan.price).toFixed(2) }}</td>
              <td class="px-4 py-2">{{ plan.image_limit }}</td>
              <td class="px-4 py-2 text-right space-x-2">
                <Link :href="route('admin.plans.edit', plan.id)" class="text-primary">Edit</Link>
                <button class="text-red-600" @click="destroyPlan(plan.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-2" v-if="plans.links">
        <Link v-for="link in plans.links" :key="link.url + link.label" :href="link.url || '#'" v-html="link.label" :class="['px-3 py-1 rounded', { 'bg-gray-200': link.active, 'opacity-50 pointer-events-none': !link.url }]" />
      </div>
    </div>
  </AppLayout>

</template>


