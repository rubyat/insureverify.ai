<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
  faqs: Array<{ q: string; a: string }>;
  contact: { email: string; statusUrl: string };
}>();

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Support', href: '/app/support' }];
</script>

<template>
  <Head title="Support" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-6 max-w-3xl">
      <h1 class="text-2xl font-semibold">Support</h1>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Search FAQs</h2>
        <input type="text" placeholder="Search..." class="mt-3 w-full rounded border px-3 py-2 text-sm" />
        <div class="mt-4 space-y-3">
          <details v-for="(f, i) in faqs" :key="i" class="rounded border p-3">
            <summary class="cursor-pointer font-medium">{{ f.q }}</summary>
            <div class="mt-2 text-sm text-muted-foreground">{{ f.a }}</div>
          </details>
        </div>
      </div>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Contact support</h2>
        <form class="mt-3 space-y-3">
          <input type="email" placeholder="Your email" class="w-full rounded border px-3 py-2 text-sm" />
          <input type="text" placeholder="Subject" class="w-full rounded border px-3 py-2 text-sm" />
          <textarea placeholder="Message" rows="5" class="w-full rounded border px-3 py-2 text-sm"></textarea>
          <button class="rounded-md bg-primary px-4 py-2 text-white">Send</button>
        </form>
        <div class="mt-3 text-sm text-muted-foreground">Or email us at <a :href="`mailto:${contact.email}`" class="underline">{{ contact.email }}</a>. Status: <a :href="contact.statusUrl" target="_blank" class="underline">status page</a>.</div>
      </div>
      </div>
    </CustomerLayout>
  </component>
</template>
