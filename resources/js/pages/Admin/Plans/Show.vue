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

      <div class="rounded border p-4 space-y-3">
        <div><span class="font-medium">Name:</span> {{ props.plan.name }}</div>
        <div><span class="font-medium">Slug:</span> {{ props.plan.slug }}</div>
        <div><span class="font-medium">Stripe Plan ID:</span> {{ props.plan.stripe_plan_id }}</div>
        <div v-if="props.plan.anet_plan_id"><span class="font-medium">Authorize.Net Plan ID:</span> {{ props.plan.anet_plan_id }}</div>
        <div>
          <span class="font-medium">Price:</span>
          <span v-if="props.plan.price !== null && props.plan.price !== undefined">${{ Number(props.plan.price).toFixed(2) }}</span>
          <span v-else>Custom Pricing</span>
        </div>
        <div><span class="font-medium">Image Limit:</span> {{ props.plan.image_limit }}</div>
        <div v-if="props.plan.verifications_included !== null && props.plan.verifications_included !== undefined">
          <span class="font-medium">Verifications Included:</span> {{ props.plan.verifications_included }}
        </div>
        <div v-if="props.plan.description">
          <span class="font-medium">Description:</span>
          <p class="mt-1 text-foreground/80">{{ props.plan.description }}</p>
        </div>
        <div v-if="Array.isArray(props.plan.features) && props.plan.features.length">
          <span class="font-medium">Features:</span>
          <ul class="list-disc ml-6 mt-1 space-y-1 text-foreground/80">
            <li v-for="(feat, idx) in props.plan.features" :key="idx">{{ feat }}</li>
          </ul>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div><span class="font-medium">CTA Label:</span> {{ props.plan.cta_label }}</div>
          <div><span class="font-medium">CTA Route:</span> {{ props.plan.cta_route }}</div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div><span class="font-medium">Sort Order:</span> {{ props.plan.sort_order }}</div>
          <div><span class="font-medium">Active:</span> {{ props.plan.is_active ? 'Yes' : 'No' }}</div>
        </div>
      </div>
    </div>
  </AppLayout>

</template>


