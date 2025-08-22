<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{ plan: any }>();

const form = useForm({
  name: props.plan.name,
  slug: props.plan.slug,
  stripe_plan_id: props.plan.stripe_plan_id,
  price: props.plan.price,
  image_limit: props.plan.image_limit,
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Plans', href: '/admin/plans' },
  { title: 'Edit', href: `/admin/plans/${props.plan.id}/edit` },
];

const submit = () => {
  form.put(route('admin.plans.update', props.plan.id));
};
</script>

<template>
  <Head title="Edit Plan" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Edit Plan</h1>
        <Link :href="route('admin.plans.index')" class="text-sm">Back</Link>
      </div>

      <form @submit.prevent="submit" class="space-y-4 max-w-xl">
        <div>
          <label class="block text-sm font-medium">Name</label>
          <input v-model="form.name" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium">Slug</label>
          <input v-model="form.slug" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="form.errors.slug" class="text-sm text-red-600">{{ form.errors.slug }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium">Stripe Plan ID</label>
          <input v-model="form.stripe_plan_id" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="form.errors.stripe_plan_id" class="text-sm text-red-600">{{ form.errors.stripe_plan_id }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium">Price</label>
          <input v-model="form.price" type="number" step="0.01" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="form.errors.price" class="text-sm text-red-600">{{ form.errors.price }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium">Image Limit</label>
          <input v-model.number="form.image_limit" type="number" min="0" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="form.errors.image_limit" class="text-sm text-red-600">{{ form.errors.image_limit }}</div>
        </div>

        <div>
          <button :disabled="form.processing" type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Save</button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>


