<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background text-foreground">
    <!-- Header -->
    <header class="border-b border-gray-200 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <Link href="/" class="font-semibold text-lg">InsureVerifyAI</Link>
        <nav class="hidden sm:flex items-center gap-6 text-sm">
          <Link href="/" class="hover:text-primary">Home</Link>
          <Link href="/plans" class="hover:text-primary">Plans</Link>
          <Link href="/login" class="hover:text-primary">Sign in</Link>
        </nav>
      </div>
      <div v-if="breadcrumbs && breadcrumbs.length" class="border-t border-gray-100 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 text-sm text-gray-600 flex items-center gap-2">
          <template v-for="(bc, idx) in breadcrumbs" :key="idx">
            <Link v-if="bc.href" :href="bc.href" class="hover:text-primary">{{ bc.title }}</Link>
            <span v-else>{{ bc.title }}</span>
            <span v-if="idx < breadcrumbs.length - 1" class="text-gray-400">/</span>
          </template>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-600 flex flex-col sm:flex-row items-center justify-between gap-2">
        <p>© {{ new Date().getFullYear() }} InsureVerifyAI. All rights reserved.</p>
        <div class="flex items-center gap-4">
          <Link href="/privacy" class="hover:text-primary">Privacy</Link>
          <Link href="/terms" class="hover:text-primary">Terms</Link>
          <Link href="/contact" class="hover:text-primary">Contact</Link>
        </div>
      </div>
    </footer>
  </div>
</template>
