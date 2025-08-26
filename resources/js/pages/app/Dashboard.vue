<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
  usage: { used: number; limit: number; resetDate: string };
  plan: { name: string; price: number; upgradeUrl: string };
  recentUploads: Array<{ id: number; thumbnail: string; filename: string; size: string; status: string; uploaded_at: string }>;
  banners: Array<{ type: 'info' | 'warning' | 'error'; message: string }>;
}>();

const percent = Math.min(100, Math.round((props.usage.used / props.usage.limit) * 100));

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Dashboard', href: '/app' }];
</script>

<template>
  <Head title="Dashboard" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <h1 class="text-2xl font-semibold">Welcome back</h1>
          <Link :href="route('app.verification')" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">Start Verification</Link>
        </div>

        <div v-if="banners?.length" class="space-y-2">
          <div v-for="(b, i) in banners" :key="i" :class="['rounded border p-3 text-sm', b.type === 'warning' ? 'border-amber-300 bg-amber-50 text-amber-900' : b.type === 'error' ? 'border-red-300 bg-red-50 text-red-900' : 'border-blue-300 bg-blue-50 text-blue-900']">
            {{ b.message }}
          </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
          <div class="rounded border p-4 md:col-span-2">
            <h2 class="text-lg font-semibold">Usage</h2>
            <div class="mt-3">
              <div class="flex justify-between text-sm text-muted-foreground">
                <span>{{ props.usage.used }} / {{ props.usage.limit }} uploads</span>
                <span>Resets {{ props.usage.resetDate }}</span>
              </div>
              <div class="mt-2 h-3 w-full overflow-hidden rounded bg-gray-200">
                <div class="h-full bg-primary" :style="{ width: percent + '%' }"></div>
              </div>
            </div>
          </div>
          <div class="rounded border p-4">
            <h2 class="text-lg font-semibold">Plan</h2>
            <div class="mt-2 text-sm text-muted-foreground">Current</div>
            <div class="text-xl font-semibold">{{ props.plan.name }}</div>
            <div class="text-sm">${{ props.plan.price.toFixed(2) }}/mo</div>
            <Link :href="props.plan.upgradeUrl" class="mt-3 inline-flex rounded-md border px-3 py-2 text-sm">Upgrade</Link>
          </div>
        </div>

        <div class="rounded border p-4 hidden">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Recent uploads</h2>
            <a :href="route('app.library')" class="text-sm text-primary">View all</a>
          </div>
          <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5">
            <div v-for="item in props.recentUploads" :key="item.id" class="group rounded border p-2">
              <img :src="item.thumbnail" :alt="item.filename" class="aspect-square w-full rounded object-cover" />
              <div class="mt-2 truncate text-sm">{{ item.filename }}</div>
              <div class="text-xs text-muted-foreground">{{ item.size }} · {{ item.status }}</div>
            </div>
          </div>
        </div>
      </div>
    </CustomerLayout>
  </component>
</template>
