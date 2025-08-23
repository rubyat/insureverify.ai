<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
  usage: { used: number; limit: number; resetDate: string };
  history: Array<{ cycle: string; used: number; limit: number }>;
  upgradeUrl: string;
}>();

const percent = Math.min(100, Math.round((props.usage.used / props.usage.limit) * 100));

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Usage & Limits', href: '/app/usage' }];
</script>

<template>
  <Head title="Usage & Limits" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-6">
      <h1 class="text-2xl font-semibold">Usage & Limits</h1>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Current cycle</h2>
        <div class="mt-3 flex items-center justify-between text-sm text-muted-foreground">
          <span>{{ props.usage.used }} / {{ props.usage.limit }} uploads</span>
          <span>Resets {{ props.usage.resetDate }}</span>
        </div>
        <div class="mt-2 h-3 w-full overflow-hidden rounded bg-gray-200">
          <div class="h-full bg-primary" :style="{ width: percent + '%' }"></div>
        </div>
        <a :href="props.upgradeUrl" class="mt-3 inline-flex rounded-md border px-3 py-2 text-sm">Need more? Upgrade plan</a>
      </div>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Recent cycles</h2>
        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
          <div v-for="h in props.history" :key="h.cycle" class="rounded border p-3">
            <div class="text-sm text-muted-foreground">{{ h.cycle }}</div>
            <div class="mt-1 text-xl font-semibold">{{ h.used }} / {{ h.limit }}</div>
            <div class="mt-2 h-2 w-full overflow-hidden rounded bg-gray-200">
              <div class="h-full bg-primary" :style="{ width: Math.min(100, Math.round((h.used / h.limit) * 100)) + '%' }"></div>
            </div>
          </div>
        </div>
      </div>
      </div>
    </CustomerLayout>
  </component>
</template>
