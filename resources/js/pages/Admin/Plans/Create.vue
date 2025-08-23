<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import PlanForm from './PlanForm.vue'

const form = useForm({
  name: '',
  slug: '',
  stripe_plan_id: '',
  anet_plan_id: '',
  price: '',
  image_limit: 0,
  description: '',
  verifications_included: '',
  features: '', // newline separated
  cta_label: 'Get Started',
  cta_route: 'plans.index',
  sort_order: 0,
  visibility: 'Public',
  is_active: true,
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Plans', href: '/admin/plans' },
  { title: 'Create', href: '/admin/plans/create' },
];

const submit = () => {
    console.log(form);
  form.post(route('admin.plans.store'));
};
</script>

<template>
  <Head title="Create Plan" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Create Plan</h1>
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
        submit-label="Create"
      />
    </div>
  </AppLayout>
</template>


