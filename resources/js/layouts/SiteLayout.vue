<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount, ref } from 'vue'
import type { BreadcrumbItemType } from '@/types';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

// Show bottom border only when header is in "stuck" state (page scrolled)
const stuck = ref(false)
const sentinel = ref<HTMLElement | null>(null)

let observer: IntersectionObserver | null = null
onMounted(() => {
  if (!sentinel.value) return
  observer = new IntersectionObserver(
    ([entry]) => {
      stuck.value = !entry.isIntersecting
    },
    { rootMargin: '0px 0px 0px 0px', threshold: [0, 1] }
  )
  observer.observe(sentinel.value)
})

onBeforeUnmount(() => {
  observer?.disconnect()
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background text-foreground">
    <!-- Sentinel used to detect when header becomes sticky -->
    <div ref="sentinel" class="h-0"></div>
    <!-- Header -->
    <header :class="['sticky top-0 z-50 bg-black text-gray-200 border-b', stuck ? 'border-sky-600' : 'border-transparent']">
      <div class="container mx-auto h-16 grid grid-cols-3 items-center">
        <!-- Left: Logo -->
        <div class="flex items-center">
          <Link :href="route('home')" class="inline-flex items-center gap-2">
            <img src="/images/logo.jpg" alt="InsureVerifyAI" class="h-12 w-auto object-contain" />
          </Link>
        </div>

        <!-- Center: Main Nav -->
        <nav class="hidden sm:flex items-center justify-center gap-6 text-sm">
          <Link :href="route('home')" class="text-gray-300 hover:text-sky-500">Home</Link>
          <Link :href="route('features')" class="text-gray-300 hover:text-sky-500">Features</Link>
          <Link :href="route('plans.index')" class="text-gray-300 hover:text-sky-500">Pricing</Link>
          <Link :href="route('contact')" class="text-gray-300 hover:text-sky-500">Contact</Link>
        </nav>

        <!-- Right: Auth Links -->
        <div class="hidden sm:flex items-center justify-end gap-3 text-sm">
          <Link :href="route('login')" class="text-gray-300 hover:text-sky-500">Login</Link>
          <Link :href="route('signup')" class="btn-primary px-4 py-2 rounded">Sign Up</Link>
        </div>
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
          <Link :href="route('privacy')" class="hover:text-primary">Privacy</Link>
          <Link :href="route('terms')" class="hover:text-primary">Terms</Link>
          <Link :href="route('contact')" class="hover:text-primary">Contact</Link>
        </div>
      </div>
    </footer>
  </div>
</template>
