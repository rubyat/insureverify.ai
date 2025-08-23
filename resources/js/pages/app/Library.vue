<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps<{
  filters: { search: string | null; date: string | null; status: string | null };
  items: Array<{ id: number; thumbnail: string; filename: string; size: string; status: string; url: string }>;
  empty: boolean;
}>();

const view = ref<'grid' | 'list'>('grid');
const search = ref(props.filters.search || '');
const status = ref<string | null>(props.filters.status);

const filtered = computed(() => props.items.filter(i => (!search.value || i.filename.toLowerCase().includes(search.value.toLowerCase())) && (!status.value || i.status === status.value)));

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Library', href: '/app/library' }];
</script>

<template>
  <Head title="Library" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-4">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold">Library</h1>
        <div class="flex items-center gap-2">
          <input v-model="search" type="text" placeholder="Search..." class="h-9 rounded border px-3 text-sm" />
          <select v-model="status" class="h-9 rounded border px-3 text-sm">
            <option :value="null">All statuses</option>
            <option value="processed">Processed</option>
            <option value="queued">Queued</option>
            <option value="failed">Failed</option>
          </select>
          <div class="ml-2 inline-flex overflow-hidden rounded border">
            <button :class="['px-3 py-1 text-sm', view === 'grid' ? 'bg-gray-100' : '']" @click="view = 'grid'">Grid</button>
            <button :class="['px-3 py-1 text-sm', view === 'list' ? 'bg-gray-100' : '']" @click="view = 'list'">List</button>
          </div>
        </div>
      </div>

      <div v-if="!filtered.length" class="rounded border p-10 text-center text-sm text-muted-foreground">
        No uploads yet → <a :href="route('app.upload')" class="underline">Upload now</a>
      </div>

      <div v-else>
        <div v-if="view === 'grid'" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
          <div v-for="item in filtered" :key="item.id" class="group rounded border p-2">
            <img :src="item.thumbnail" :alt="item.filename" class="aspect-square w-full rounded object-cover" />
            <div class="mt-2 truncate text-sm">{{ item.filename }}</div>
            <div class="text-xs text-muted-foreground">{{ item.size }} · {{ item.status }}</div>
            <div class="mt-2 flex gap-2 text-xs">
              <a :href="item.url" target="_blank" class="rounded border px-2 py-1">View</a>
              <a :href="item.url" download class="rounded border px-2 py-1">Download</a>
              <button class="rounded border px-2 py-1 text-red-600">Delete</button>
            </div>
          </div>
        </div>

        <div v-else class="divide-y rounded border">
          <div v-for="item in filtered" :key="item.id" class="flex items-center justify-between p-3">
            <div class="flex items-center gap-3 min-w-0">
              <img :src="item.thumbnail" :alt="item.filename" class="h-12 w-12 rounded object-cover" />
              <div class="min-w-0">
                <div class="truncate text-sm">{{ item.filename }}</div>
                <div class="text-xs text-muted-foreground">{{ item.size }} · {{ item.status }}</div>
              </div>
            </div>
            <div class="flex gap-2 text-xs">
              <a :href="item.url" target="_blank" class="rounded border px-2 py-1">View</a>
              <a :href="item.url" download class="rounded border px-2 py-1">Download</a>
              <button class="rounded border px-2 py-1 text-red-600">Delete</button>
            </div>
          </div>
        </div>
      </div>
      </div>
    </CustomerLayout>
  </component>
</template>
