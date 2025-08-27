<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount, ref, computed } from 'vue'
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
const menuOpen = ref(false)
const mobileNavOpen = ref(false)
const mobileAccountOpen = ref(false)

const page = usePage()
const authUser = computed(() => (page.props as any)?.auth?.user)
// Settings shared via Inertia from config('settings')
const settings = computed(() => (page.props as any)?.settings ?? {})
const copyrightText = computed(() => settings.value?.copyright || `© ${new Date().getFullYear()} InsureVerifyAI. All rights reserved.`)

function onDocumentClick(e: MouseEvent) {
  const target = e.target as HTMLElement
  const menu = document.getElementById('user-menu')
  const trigger = document.getElementById('user-menu-button')
  if (!menu || !trigger) return
  if (!menu.contains(target) && !trigger.contains(target)) {
    menuOpen.value = false
  }
}

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
  document.addEventListener('click', onDocumentClick)
})

onBeforeUnmount(() => {
  observer?.disconnect()
  document.removeEventListener('click', onDocumentClick)
})

// Smooth expand/collapse transitions for mobile panels
function panelEnter(el: Element) {
  const e = el as HTMLElement
  e.style.height = '0px'
  e.style.opacity = '0'
  e.style.overflow = 'hidden'
  // measure target height
  const target = e.scrollHeight
  // start transition
  requestAnimationFrame(() => {
    e.style.transition = 'height 220ms ease, opacity 220ms ease'
    e.style.height = target + 'px'
    e.style.opacity = '1'
  })
}

function panelAfterEnter(el: Element) {
  const e = el as HTMLElement
  e.style.height = ''
  e.style.opacity = ''
  e.style.transition = ''
  e.style.overflow = ''
}

function panelLeave(el: Element) {
  const e = el as HTMLElement
  e.style.overflow = 'hidden'
  e.style.height = e.scrollHeight + 'px'
  e.style.opacity = '1'
  requestAnimationFrame(() => {
    e.style.transition = 'height 180ms ease, opacity 180ms ease'
    e.style.height = '0px'
    e.style.opacity = '0'
  })
}

function panelAfterLeave(el: Element) {
  const e = el as HTMLElement
  e.style.height = ''
  e.style.opacity = ''
  e.style.transition = ''
  e.style.overflow = ''
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background text-foreground">
    <!-- Sentinel used to detect when header becomes sticky -->
    <div ref="sentinel" class="h-0"></div>
    <!-- Header -->
    <header :class="['sticky top-0 z-50 px-6 md:px-12 bg-black text-gray-200 border-b', stuck ? 'border-sky-600' : 'border-transparent']">
      <div class="container mx-auto h-16 flex justify-between">
        <!-- Left: Logo -->
        <div class="flex items-center">
          <Link :href="route('home')" class="inline-flex items-center gap-2">
            <img :src="settings.logo || '/images/insureverify-ai-logo.png'" alt="InsureVerifyAI" class="h-12 w-auto object-contain" />
          </Link>
        </div>

        <!-- Center: Main Nav -->
        <nav class="hidden sm:flex items-center justify-center gap-6 text-sm">

          <Link :href="route('features')" class="text-gray-300 hover:text-sky-500">Features</Link>
          <Link :href="route('plans.index')" class="text-gray-300 hover:text-sky-500">Pricing</Link>
          <Link :href="route('contact')" class="text-gray-300 hover:text-sky-500">Contact</Link>
        </nav>

        <!-- Right: Auth Links / User Dropdown -->
        <div class="hidden sm:flex items-center justify-end gap-3 text-sm relative">
          <template v-if="authUser">
            <button id="user-menu-button" type="button" @click="menuOpen = !menuOpen" class="inline-flex items-center gap-2 px-3 py-2 rounded hover:bg-white/10 focus:outline-none">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                <i class="fa-regular fa-user"></i>
              </span>
              <span class="hidden md:inline text-gray-200">{{ authUser.name ?? (authUser.first_name + ' ' + authUser.last_name) }}</span>
              <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
            </button>
            <div id="user-menu" v-show="menuOpen" class="absolute right-0 top-12 w-48 bg-white text-gray-700 rounded-md shadow border border-gray-200 py-1 z-50">
              <Link :href="route('app.dashboard')" class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50">
                <i class="fa-solid fa-house w-4 text-gray-500"></i>
                <span>Dashboard</span>
              </Link>
              <Link :href="route('profile.edit')" class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50">
                <i class="fa-regular fa-id-card w-4 text-gray-500"></i>
                <span>Profile</span>
              </Link>
              <Link :href="route('appearance')" class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50">
                <i class="fa-solid fa-gear w-4 text-gray-500"></i>
                <span>Settings</span>
              </Link>
              <Link :href="route('logout')" method="post" as="button" class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-gray-50">
                <i class="fa-solid fa-arrow-right-from-bracket w-4 text-gray-500"></i>
                <span>Logout</span>
              </Link>
            </div>
          </template>
          <template v-else>
            <Link :href="route('login')" class="text-gray-300 hover:text-sky-500">Login</Link>
            <Link :href="route('signup')" class="btn-primary px-4 py-2 rounded">Sign Up</Link>
          </template>
        </div>

        <!-- Mobile: Hamburger + Account -->
        <div class="sm:hidden flex items-center gap-2">
          <button
            type="button"
            class="inline-flex items-center justify-center rounded-md p-2 text-gray-300 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500"
            aria-label="Open main menu"
            @click="mobileNavOpen = !mobileNavOpen"
          >
            <svg v-if="!mobileNavOpen" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            <svg v-else class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>

          <button
            type="button"
            class="inline-flex items-center justify-center rounded-md p-2 text-gray-300 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-sky-500"
            aria-label="Open account menu"
            @click="mobileAccountOpen = !mobileAccountOpen"
          >

            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                <i class="fa-regular fa-user"></i>
            </span>
          </button>
        </div>
      </div>


      <!-- Mobile: Collapsible Nav -->
      <Transition :css="false" @enter="panelEnter" @after-enter="panelAfterEnter" @leave="panelLeave" @after-leave="panelAfterLeave">
        <div v-show="mobileNavOpen" class="sm:hidden border-t border-white/10 bg-black/95 text-gray-200 transform">
          <div class="px-4 py-3 space-y-2 text-sm">
            <div class="grid grid-cols-1 gap-2 text-right">
              <Link :href="route('home')" class="block px-3 py-2 rounded hover:bg-white/10">Home</Link>
              <Link :href="route('features')" class="block px-3 py-2 rounded hover:bg-white/10">Features</Link>
              <Link :href="route('plans.index')" class="block px-3 py-2 rounded hover:bg-white/10">Pricing</Link>
              <Link :href="route('contact')" class="block px-3 py-2 rounded hover:bg-white/10">Contact</Link>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Mobile: Collapsible Account -->
      <Transition :css="false" @enter="panelEnter" @after-enter="panelAfterEnter" @leave="panelLeave" @after-leave="panelAfterLeave">
        <div v-show="mobileAccountOpen" class="sm:hidden border-t border-white/10 bg-black/95 text-gray-200 transform">
          <div class="px-4 py-3 space-y-2 text-sm">
            <template v-if="authUser">
              <div class="grid grid-cols-1 gap-2 text-right">
                <Link :href="route('app.dashboard')" class="block px-3 py-2 rounded hover:bg-white/10">
                  <span class="flex items-center justify-end gap-2"><span>Dashboard</span><i class="fa-solid fa-gauge w-4"></i></span>
                </Link>
                <Link :href="route('profile.edit')" class="block px-3 py-2 rounded hover:bg-white/10">
                  <span class="flex items-center justify-end gap-2"><span>Profile</span><i class="fa-regular fa-id-card w-4"></i></span>
                </Link>
                <Link :href="route('appearance')" class="block px-3 py-2 rounded hover:bg-white/10">
                  <span class="flex items-center justify-end gap-2"><span>Settings</span><i class="fa-solid fa-gear w-4"></i></span>
                </Link>
                <Link :href="route('logout')" method="post" as="button" class="block w-full px-3 py-2 rounded hover:bg-white/10 text-right">
                  <span class="flex items-center justify-end gap-2"><span>Logout</span><i class="fa-solid fa-arrow-right-from-bracket w-4"></i></span>
                </Link>
              </div>
            </template>
            <template v-else>
              <div class="grid grid-cols-1 gap-2 text-right">
                <Link :href="route('login')" class="block px-3 py-2 rounded hover:bg-white/10">
                  <span class="flex items-center justify-end gap-2"><span>Login</span><i class="fa-solid fa-right-to-bracket w-4"></i></span>
                </Link>
                <Link :href="route('signup')" class="block px-3 py-2 rounded hover:bg-white/10">
                  <span class="flex items-center justify-end gap-2"><span>Sign Up</span><i class="fa-solid fa-user-plus w-4"></i></span>
                </Link>
              </div>
            </template>
          </div>
        </div>
      </Transition>


      <div v-if="breadcrumbs && breadcrumbs.length" class="border-t border-gray-100 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 text-sm text-gray-600 flex items-center gap-2 overflow-x-auto whitespace-nowrap">
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
    <footer class="bg-black text-white pt-12 sm:pt-16 pb-6 sm:pb-6 px-6 md:px-12">
      <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-10">
        <!-- Brand and description -->
        <div class="max-w-xl">
          <Link :href="route('home')" class="text-2xl font-bold text-white inline-block">
            <img src="/images/insureverify-ai-logo.png" alt="InsureVerifyAI Logo" class="object-contain h-10 w-auto" />
          </Link>
          <p class="text-gray-400 mt-4 text-sm leading-6">
            {{ settings.footer_description }}
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <p class="text-white font-semibold mb-4">Quick Links</p>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><Link href="/about-us" class="hover:text-sky-500">About us</Link></li>
            <li><Link href="/pricing" class="hover:text-sky-500">Pricing</Link></li>
            <li><Link href="/docs" class="hover:text-sky-500">API Documentation</Link></li>
          </ul>
          <div class="flex flex-wrap gap-3 mt-6">
            <a v-if="settings.social?.facebook" class="bg-white/10 hover:bg-sky-600 text-white p-2 rounded-full transition" aria-label="Facebook" :href="settings.social.facebook" target="_blank" rel="noopener noreferrer">
              <svg stroke="currentColor" fill="currentColor" viewBox="0 0 320 512" height="16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
            </a>
            <a v-if="settings.social?.linkedin" class="bg-white/10 hover:bg-sky-600 text-white p-2 rounded-full transition" aria-label="LinkedIn" :href="settings.social.linkedin" target="_blank" rel="noopener noreferrer">
              <svg stroke="currentColor" fill="currentColor" viewBox="0 0 448 512" height="16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg>
            </a>
            <a v-if="settings.social?.twitter" class="bg-white/10 hover:bg-sky-600 text-white p-2 rounded-full transition" aria-label="X (Twitter)" :href="settings.social.twitter" target="_blank" rel="noopener noreferrer">
              <svg stroke="currentColor" fill="currentColor" viewBox="0 0 512 512" height="16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
            </a>
            <a v-if="settings.social?.instagram" class="bg-white/10 hover:bg-sky-600 text-white p-2 rounded-full transition" aria-label="Instagram" :href="settings.social.instagram" target="_blank" rel="noopener noreferrer">
              <svg stroke="currentColor" fill="currentColor" viewBox="0 0 448 512" height="16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
            </a>
            <a v-if="settings.social?.youtube" class="bg-white/10 hover:bg-sky-600 text-white p-2 rounded-full transition" aria-label="YouTube" :href="settings.social.youtube" target="_blank" rel="noopener noreferrer">
              <svg stroke="currentColor" fill="currentColor" viewBox="0 0 576 512" height="16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.624 64 288 64 288 64s-170.624 0-213.371 11.486c-23.497 6.322-42.003 24.947-48.284 48.597C16.859 166.916 16 224 16 224s.859 57.084 10.345 99.917c6.281 23.65 24.787 42.276 48.284 48.597C117.376 384 288 384 288 384s170.624 0 213.371-11.486c23.497-6.322 42.003-24.947 48.284-48.597C559.141 281.084 560 224 560 224s-.859-57.084-10.345-99.917zM232 312V136l142 88-142 88z"/></svg>
            </a>
          </div>
        </div>

        <!-- Legal -->
        <div>
          <p class="text-white font-semibold mb-4">Legal</p>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><Link href="/privacy-policy" class="hover:text-sky-500">Privacy Policy</Link></li>
            <li><Link href="/terms-of-service" class="hover:text-sky-500">Terms of Service</Link></li>
            <li><Link href="/faq" class="hover:text-sky-500">FAQ</Link></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <p class="text-white font-semibold mb-4">Contact Us</p>
          <p v-if="settings.contact_email" class="text-sm text-gray-400 mb-1">Email: {{ settings.contact_email }}</p>
          <p v-if="settings.business_hours" class="text-sm text-gray-400 mb-1">Business Hours: {{ settings.business_hours }}</p>
        </div>
      </div>
      <div class="mt-10 sm:mt-12 border-t border-gray-800 pt-6 text-center text-xs sm:text-sm text-gray-500">{{ copyrightText }}</div>
    </footer>
  </div>
</template>
