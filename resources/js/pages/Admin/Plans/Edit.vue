<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import PlanForm from './PlanForm.vue'

const props = defineProps<{ plan: any }>();

const form = useForm({
  name: props.plan.name,
  slug: props.plan.slug,
  stripe_plan_id: props.plan.stripe_plan_id,
  anet_plan_id: props.plan.anet_plan_id ?? '',
  price: props.plan.price,
  image_limit: props.plan.image_limit,
  description: props.plan.description ?? '',
  verifications_included: props.plan.verifications_included ?? '',
  features: Array.isArray(props.plan.features) ? props.plan.features.join('\n') : (props.plan.features ?? ''),
  cta_label: props.plan.cta_label ?? 'Get Started',
  cta_route: props.plan.cta_route ?? 'plans.index',
  sort_order: props.plan.sort_order ?? 0,
  visibility: props.plan.visibility ?? 'Public',
  is_active: props.plan.is_active ?? true,
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
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Edit Plan</h1>
        <Link :href="route('admin.plans.index')" class="text-sm">Back</Link>
      </div>

      <PlanForm
        v-model:name="form.name"
        v-model:slug="form.slug"
        v-model:stripe_plan_id="form.stripe_plan_id"
        v-model:anet_plan_id="form.anet_plan_id"
        v-model:price="form.price"
        v-model:image_limit="form.image_limit"
        v-model:description="form.description"
        v-model:verifications_included="form.verifications_included"
        v-model:features="form.features"
        v-model:cta_label="form.cta_label"
        v-model:cta_route="form.cta_route"
        v-model:sort_order="form.sort_order"
        v-model:visibility="form.visibility"
        v-model:is_active="form.is_active"
        :errors="form.errors"
        :processing="form.processing"
        :on-submit="submit"
        submit-label="Save"
      />
    </div>
  </AppLayout>
</template>


