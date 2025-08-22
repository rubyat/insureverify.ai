<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{ plan: any }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Plans', href: '/admin/plans' },
  { title: props.plan.name, href: `/admin/plans/${props.plan.id}` },
];
</script>

<template>
  <Head :title="`Plan: ${props.plan.name}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-4 max-w-2xl">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Plan Details</h1>
        <Link :href="route('admin.plans.index')" class="text-sm">Back</Link>
      </div>

      <div class="rounded border p-4 space-y-2">
        <div><span class="font-medium">Name:</span> {{ props.plan.name }}</div>
        <div><span class="font-medium">Slug:</span> {{ props.plan.slug }}</div>
        <div><span class="font-medium">Stripe Plan ID:</span> {{ props.plan.stripe_plan_id }}</div>
        <div><span class="font-medium">Price:</span> ${{ Number(props.plan.price).toFixed(2) }}</div>
        <div><span class="font-medium">Image Limit:</span> {{ props.plan.image_limit }}</div>
      </div>
    </div>
  </AppLayout>

</template>


