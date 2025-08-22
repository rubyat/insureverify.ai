
# InsureVerifyAI — Inertia Vue + Tailwind Scaffold (Windfurf Context)

Use this markdown to have Windfurf generate a production-ready Laravel + Inertia + Vue 3 frontend scaffold that matches the provided visual system.

---

## 1) Recommended Directory Structure

```text
resources/
  js/
    App.vue
    app.js
    Pages/
      Home.vue
      Features.vue
      Pricing.vue
      About.vue
      Docs.vue
      Contact.vue
      PrivacyPolicy.vue
      Signup.vue
    Layouts/
      AppLayout.vue
    Components/
      NavBar.vue
      MobileMenu.vue
      FooterBar.vue
      Hero.vue
      CTASection.vue
      FeatureCard.vue
      PricingTierCard.vue
      PartnerLogo.vue
      SectionTitle.vue
```

> Notes
- **AppLayout.vue** wraps all pages (sticky black navbar + black footer).
- **NavBar.vue** contains desktop links and a hamburger that toggles **MobileMenu.vue**.
- **FooterBar.vue** has 4 columns + social icons.
- **PricingTierCard.vue**, **FeatureCard.vue**, **PartnerLogo.vue** are small presentational components.

---

## 2) Shared Layout

```vue
<!-- resources/js/Layouts/AppLayout.vue -->
<script setup>
import NavBar from '@/Components/NavBar.vue'
import FooterBar from '@/Components/FooterBar.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white text-black">
    <NavBar />
    <main class="flex-1">
      <slot />
    </main>
    <FooterBar />
  </div>
</template>
```

---

## 3) Core Components

```vue
<!-- resources/js/Components/NavBar.vue -->
<script setup>
import { ref } from 'vue'
const open = ref(false)
</script>

<template>
  <header class="bg-black sticky top-0 z-50 shadow-sm">
    <div class="max-w-screen mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="/" class="flex items-center">
        <img src="/images/logo.svg" alt="InsureVerifyAI" class="h-8 object-contain" />
      </a>

      <nav class="hidden md:flex space-x-6">
        <a href="/features" class="text-gray-300 hover:text-sky-500 transition">Features</a>
        <a href="/pricing" class="text-gray-300 hover:text-sky-500 transition">Pricing</a>
        <a href="/contact" class="text-gray-300 hover:text-sky-500 transition">Contact</a>
      </nav>

      <div class="hidden md:flex items-center space-x-2">
        <a href="/login" class="px-4 py-2 text-gray-300 hover:opacity-80">Login</a>
        <a href="/signup" class="px-4 py-2 rounded btn-primary">Sign up</a>
      </div>

      <button class="md:hidden text-white" @click="open = !open" aria-label="Open menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    <div v-if="open" class="md:hidden border-t border-white/10">
      <div class="px-4 py-2 space-y-2">
        <a href="/features" class="block text-gray-200 py-2">Features</a>
        <a href="/pricing" class="block text-gray-200 py-2">Pricing</a>
        <a href="/contact" class="block text-gray-200 py-2">Contact</a>
        <a href="/login" class="block text-gray-200 py-2">Login</a>
        <a href="/signup" class="block btn-primary rounded inline-block px-4 py-2">Sign up</a>
      </div>
    </div>
  </header>
</template>
```

```vue
<!-- resources/js/Components/FooterBar.vue -->
<template>
  <footer class="bg-black text-white px-6 md:px-12 py-16">
    <div class="max-w-screen mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-16">
      <div>
        <img src="/images/logo-white.svg" class="h-10" alt="InsureVerifyAI" />
        <p class="text-gray-400 text-sm mt-4">
          InsureVerifyAI helps car rental companies and mobility platforms instantly verify driving licenses and insurance.
        </p>
      </div>

      <div>
        <h3 class="font-semibold mb-4">Quick Links</h3>
        <ul class="space-y-2 text-sm text-gray-400">
          <li><a href="/about-us" class="hover:text-sky-500">About us</a></li>
          <li><a href="/pricing" class="hover:text-sky-500">Pricing</a></li>
          <li><a href="/docs" class="hover:text-sky-500">API Documentation</a></li>
        </ul>
      </div>

      <div>
        <h3 class="font-semibold mb-4">Legal</h3>
        <ul class="space-y-2 text-sm text-gray-400">
          <li><a href="/privacy-policy" class="hover:text-sky-500">Privacy Policy</a></li>
          <li><a href="/terms-of-service" class="hover:text-sky-500">Terms of Service</a></li>
        </ul>
      </div>

      <div>
        <h3 class="font-semibold mb-4">Contact Us</h3>
        <p class="text-sm text-gray-400">Email: support@insureverify.ai</p>
        <p class="text-sm text-gray-400">Business Hours: Mon–Fri, 9 AM – 6 PM (EST)</p>
      </div>
    </div>
    <div class="mt-12 border-t border-gray-800 pt-6 text-center text-sm text-gray-500">
      © 2025 InsureVerifyAI. All rights reserved.
    </div>
  </footer>
</template>
```

```vue
<!-- resources/js/Components/SectionTitle.vue -->
<script setup>
defineProps({ eyebrow: String, title: String, subtitle: String, center: { type: Boolean, default: true } })
</script>

<template>
  <div :class="center ? 'text-center' : ''">
    <p v-if="eyebrow" class="text-sm font-semibold uppercase tracking-wide text-sky-500">{{ eyebrow }}</p>
    <h2 class="text-3xl md:text-4xl font-bold mt-2">{{ title }}</h2>
    <p v-if="subtitle" class="text-gray-600 mt-3 max-w-2xl mx-auto">{{ subtitle }}</p>
  </div>
</template>
```

```vue
<!-- resources/js/Components/PricingTierCard.vue -->
<script setup>
defineProps({ name: String, price: String, perks: Array, ctaHref: String, ctaText: String })
</script>

<template>
  <div class="border border-black p-8 rounded-lg flex flex-col justify-between">
    <div>
      <h3 class="text-xl font-semibold mb-1 text-left">{{ name }}</h3>
      <div class="flex items-baseline space-x-2 mb-2">
        <span class="text-3xl font-bold">{{ price }}</span>
        <span class="text-sm text-gray-600">/month</span>
      </div>
      <ul class="text-gray-800 text-sm space-y-3 text-left">
        <li v-for="(perk, i) in perks" :key="i" class="flex items-start">
          <span class="w-2 h-2 mt-2 mr-2 bg-black rounded-full"></span>
          <span>{{ perk }}</span>
        </li>
      </ul>
    </div>
    <a :href="ctaHref" class="mt-8 btn-primary py-2 px-4 rounded text-center block hover:bg-gray-900 transition">
      {{ ctaText }}
    </a>
  </div>
</template>
```

---

## 4) Pages (skeletons)

### Home.vue
```vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue'
import PricingTierCard from '@/Components/PricingTierCard.vue'
</script>

<template>
  <AppLayout>
    <!-- Hero -->
    <section class="relative min-h-screen bg-cover bg-center flex items-center px-6" style="background-image:url('/images/hero.jpg')">
      <div class="absolute inset-0 bg-black/30"></div>
      <div class="relative z-10 container mx-auto grid grid-cols-1 md:grid-cols-5 gap-8 items-center">
        <div class="md:col-span-3 text-white space-y-6 pl-7">
          <h1 class="text-4xl sm:text-5xl font-extrabold">Fast, Reliable License & Insurance Verification API for Car Rentals</h1>
          <p class="text-lg max-w-xl">Verify renters’ driving licenses and insurance in seconds. Reduce risk, save time, and boost ROI.</p>
          <a href="/signup" class="px-6 py-3 btn-primary rounded-md">Request API Access</a>
        </div>
      </div>
    </section>

    <!-- Why Choose -->
    <section class="bg-white py-20 px-6 md:px-12">
      <SectionTitle title="Why Choose InsureVerifyAI?" subtitle="Smarter Verifications. Safer Rentals." />
      <!-- Feature grid would use FeatureCard.vue -->
    </section>

    <!-- Pricing -->
    <section class="bg-white py-20 px-6 md:px-12">
      <SectionTitle title="Pricing" subtitle="Simple, transparent pricing that scales with your business" />
      <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-5">
        <PricingTierCard name="Silver" price="$99" :perks="['No monthly minimum','Access to all verification features','Real-time checks','Email & chat support']" ctaHref="/signup?plan=Silver" ctaText="Get Started" />
        <PricingTierCard name="Bronze" price="$199" :perks="['Priority API access','Full audit trail','Dedicated account manager']" ctaHref="/signup?plan=Bronze" ctaText="Get Started" />
        <PricingTierCard name="Gold" price="$349" :perks="['Identity verification','License validation','Insurance coverage check','Real-time API response','Fraud prevention','Basic analytics']" ctaHref="/signup?plan=Gold" ctaText="Get Started" />
        <PricingTierCard name="Platinum" price="$499" :perks="['Identity verification','License & insurance checks','AI voice confirmation','Call recording','Advanced fraud prevention','Customizable flow','Priority support']" ctaHref="/contact?plan=Platinum" ctaText="Request Access" />
        <PricingTierCard name="Enterprise" price="Custom" :perks="['Volume-based discounts','Custom SLAs','Usage insights','Premium onboarding']" ctaHref="/contact?plan=Enterprise" ctaText="Contact us" />
      </div>
    </section>

    <!-- CTA -->
    <section class="bg-black text-white py-16 px-6 md:px-12 text-center">
      <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Approve More Renters in Seconds?</h2>
      <p class="text-gray-300 mb-8 max-w-2xl mx-auto">Start verifying driving licenses and insurance instantly with InsureVerifyAI.</p>
      <a href="/signup" class="btn-primary px-6 py-3 rounded-md">Get Started</a>
    </section>
  </AppLayout>
</template>
```

### Features.vue
```vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue'
</script>

<template>
  <AppLayout>
    <section class="py-16 px-6 md:px-12">
      <SectionTitle title="All the Features You Need" subtitle="From performance to security and support." />
      <div class="grid md:grid-cols-2 gap-10 mt-10">
        <!-- 4 feature items; icon + title + copy -->
      </div>
      <div class="text-center mt-10">
        <a href="/signup" class="btn-primary px-6 py-3 rounded-md">Request API Access</a>
      </div>
    </section>
  </AppLayout>
</template>
```

### Pricing.vue
```vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue'
import PricingTierCard from '@/Components/PricingTierCard.vue'
</script>

<template>
  <AppLayout>
    <section class="py-16 px-6 md:px-12">
      <SectionTitle title="Simple, Transparent Pricing" subtitle="Choose a plan that fits your needs. Cancel anytime." />
      <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-5">
        <!-- Reuse PricingTierCard as in Home.vue -->
      </div>
    </section>
  </AppLayout>
</template>
```

### About.vue, Docs.vue, Contact.vue, PrivacyPolicy.vue, Signup.vue
Provide simple sections mirroring the copy in the context doc. For **Contact.vue**, include a form with fields: name, email, message. For **Signup.vue**, include form fields for plan, name, business email, password/confirm and payment info (cc, exp, cvv) plus a checkbox for T&C.

---

## 5) Tailwind helpers

Add a small **btn-primary** utility in your CSS:

```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;

.btn-primary {
  @apply bg-sky-500 text-white hover:bg-sky-600 transition;
}
```

---

## 6) Windfurf Prompt Hint

> “Use this file to generate the Vue components and pages. Keep styles Tailwind-only, adhere to black/white/sky-500 color system, and follow the structure exactly. Pages must import **AppLayout** and wrap their content in it.”

---
