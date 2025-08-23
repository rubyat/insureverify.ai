<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
  remainingUploads: number;
  cycleResetDate: string;
  queue: Array<{ id: number; name: string; size: string; progress: number; error?: string }>;
  atLimit: boolean;
  upgradeUrl: string;
}>();

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Upload', href: '/app/upload' }];
</script>

<template>
  <Head title="Upload" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Upload</h1>
        <div class="text-sm text-muted-foreground">Remaining: {{ remainingUploads }} · Resets {{ cycleResetDate }}</div>
      </div>

      <div :class="['flex flex-col items-center justify-center rounded border-2 border-dashed p-10 text-center', atLimit ? 'opacity-60' : '']">
        <div class="text-lg font-medium">Drag & drop files here</div>
        <div class="mt-1 text-sm text-muted-foreground">or</div>
        <button class="mt-2 rounded-md bg-primary px-4 py-2 text-white" :disabled="atLimit">Choose files</button>
        <div v-if="atLimit" class="mt-3 text-sm text-red-600">You've reached your quota. <a :href="upgradeUrl" class="underline">Upgrade your plan</a>.</div>
      </div>

      <div class="rounded border">
        <div class="border-b p-3 font-medium">Upload Queue</div>
        <div v-if="queue?.length" class="divide-y">
          <div v-for="item in queue" :key="item.id" class="p-3">
            <div class="flex items-center justify-between">
              <div class="truncate">{{ item.name }} <span class="text-sm text-muted-foreground">({{ item.size }})</span></div>
              <div class="text-sm">{{ item.progress }}%</div>
            </div>
            <div class="mt-2 h-2 w-full overflow-hidden rounded bg-gray-200">
              <div class="h-full bg-primary" :style="{ width: item.progress + '%' }"></div>
            </div>
            <div v-if="item.error" class="mt-2 text-sm text-red-600">{{ item.error }}</div>
          </div>
        </div>
        <div v-else class="p-6 text-sm text-muted-foreground">No files in queue.</div>
      </div>
      </div>
    </CustomerLayout>
  </component>
</template>
