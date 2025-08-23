<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
  notifications: Array<{ id: number; type: string; message: string; read: boolean; date: string }>;
}>();

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Notifications', href: '/app/notifications' }];
</script>

<template>
  <Head title="Notifications" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-6 max-w-3xl">
      <h1 class="text-2xl font-semibold">Notifications</h1>

      <div class="rounded border">
        <div class="border-b p-3 text-sm text-muted-foreground">System messages</div>
        <div class="divide-y">
          <div v-for="n in notifications" :key="n.id" class="flex items-start justify-between gap-3 p-3">
            <div class="min-w-0">
              <div class="text-sm" :class="n.read ? 'text-muted-foreground' : ''">{{ n.message }}</div>
              <div class="text-xs text-muted-foreground">{{ n.type }} · {{ n.date }}</div>
            </div>
            <div class="shrink-0">
              <button class="rounded border px-2 py-1 text-xs">Mark as read</button>
            </div>
          </div>
        </div>
        <div class="p-3">
          <button class="rounded border px-3 py-2 text-sm">Clear all</button>
        </div>
      </div>
      </div>
    </CustomerLayout>
  </component>
</template>
