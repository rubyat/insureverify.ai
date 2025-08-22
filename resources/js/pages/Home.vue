<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Link, Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Home" />
  <SiteLayout>
    <!-- Hero -->
    <section class="relative min-h-[70vh] bg-cover bg-center flex items-center px-6 md:px-12" style="background-image:url('/images/hero-image.jpg')">
      <div class="absolute inset-0 bg-black/40"></div>
      <div class="container mx-auto">
        <div class="relative z-10 max-w-6xl">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white max-w-3xl">Fast, Reliable License & Insurance Verification API for Car Rentals</h1>
            <p class="text-white/90 text-lg max-w-2xl mt-4">Verify renters’ licenses and insurance in seconds. Reduce risk, save time, and boost ROI.</p>
            <div class="mt-8 flex gap-3">
            <Link :href="route('signup')" class="btn-primary px-6 py-3 rounded-md">Request API Access</Link>
            <Link :href="route('plans.index')" class="px-6 py-3 rounded-md bg-white text-black hover:bg-gray-100">See Pricing</Link>
            </div>
        </div>
      </div>
    </section>

    <!-- Why Choose -->
    <section class="bg-background py-16 px-6 md:px-12">
      <div class="text-center max-w-5xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold">Why Choose InsureVerifyAI?</h2>
        <p class="text-foreground/70 mt-2">Smarter Verifications. Safer Rentals.</p>
        <div class="inline-block bg-primary text-primary-foreground px-6 py-4 rounded-lg shadow mt-8">
          <p class="text-2xl md:text-3xl font-extrabold leading-tight">$3,000,000</p>
          <p class="text-xs md:text-sm uppercase tracking-wider opacity-80">Fraud-Related Theft Coverage</p>
        </div>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mt-12 max-w-6xl mx-auto">
        <div class="text-center space-y-2">
          <div class="w-10 h-10 rounded-full bg-black text-white mx-auto flex items-center justify-center">1</div>
          <h3 class="font-semibold">Identity & License Verification (AI + Biometrics)</h3>
          <p class="text-sm text-foreground/70">CLEAR-like verification with liability protection.</p>
        </div>
        <div class="text-center space-y-2">
          <div class="w-10 h-10 rounded-full bg-black text-white mx-auto flex items-center justify-center">2</div>
          <h3 class="font-semibold">Real-Time Insurance Verification</h3>
          <p class="text-sm text-foreground/70">Instant checks of declarations pages.</p>
        </div>
        <div class="text-center space-y-2">
          <div class="w-10 h-10 rounded-full bg-black text-white mx-auto flex items-center justify-center">3</div>
          <h3 class="font-semibold">Recorded Insurance Confirmation</h3>
          <p class="text-sm text-foreground/70">Optional recorded proof of coverage.</p>
        </div>
        <div class="text-center space-y-2">
          <div class="w-10 h-10 rounded-full bg-black text-white mx-auto flex items-center justify-center">4</div>
          <h3 class="font-semibold">Scalable Growth</h3>
          <p class="text-sm text-foreground/70">API grows with your business.</p>
        </div>
      </div>
    </section>

    <!-- CTA Banner -->
    <section class="bg-black text-white py-16 text-center px-6">
      <h2 class="text-3xl md:text-4xl font-bold">Ready to Approve More Renters in Seconds?</h2>
      <p class="text-gray-300 mt-2 max-w-2xl mx-auto">Start verifying driving licenses and insurance instantly.</p>
      <Link :href="route('signup')" class="btn-primary px-6 py-3 rounded-md inline-block mt-8">Get Started</Link>
    </section>

    <!-- Pricing Teaser -->
    <section class="bg-background py-16 px-6 md:px-12">
      <div class="text-center">
        <h2 class="text-3xl md:text-4xl font-bold">Pricing</h2>
        <p class="text-foreground/70 mt-2">Simple, transparent pricing that scales with your business</p>
        <Link :href="route('plans.index')" class="btn-primary px-6 py-3 rounded-md inline-block mt-8">View Plans</Link>
      </div>
    </section>

    <!-- Partners -->
    <section class="bg-white text-[#0a0a08] py-16 px-6 md:px-12">
      <div class="max-w-6xl mx-auto grid sm:grid-cols-2 lg:grid-cols-6 gap-6 items-center">
        <img src="/logo.svg" alt="Partner" class="h-10 object-contain mx-auto" v-for="i in 6" :key="i" />
      </div>
    </section>
  </SiteLayout>
</template>
