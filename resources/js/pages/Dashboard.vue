<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
];

const form = useForm<{ image: File | null }>({ image: null });
const errors = usePage().props.errors as Record<string, string>;

const submit = () => {
  const data = new FormData();
  if (form.image) data.append('image', form.image as unknown as Blob);
  form.post(route('images.store'), { forceFormData: true });
};
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-8">
      <div v-if="(usePage().props as any).usage" class="rounded border p-4">
        <p class="text-sm">You have used {{ (usePage().props as any).usage.used }} of {{ (usePage().props as any).usage.limit }} images this month.</p>
      </div>
      <div>
        <h2 class="text-lg font-semibold">Upload image</h2>
        <form @submit.prevent="submit" class="mt-4 flex items-center gap-4">
          <input type="file" accept="image/*" @change="(e:any)=>form.image = e.target.files?.[0] ?? null" class="block" />
          <button type="submit" :disabled="form.processing" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Upload</button>
        </form>
        <p v-if="errors?.image" class="mt-2 text-sm text-red-600">{{ errors.image }}</p>
        <p v-if="(usePage().props as any).flash?.success" class="mt-2 text-sm text-green-600">{{ (usePage().props as any).flash.success }}</p>
      </div>

      <div>
        <h2 class="text-lg font-semibold mb-4">Your images</h2>
        <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <div v-for="img in (usePage().props as any).images || []" :key="img.id" class="aspect-square overflow-hidden rounded border">
            <img :src="img.url" alt="Uploaded image" class="h-full w-full object-cover" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
