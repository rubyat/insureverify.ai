<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/SiteLayout.vue';

const props = defineProps<{ plans: Array<any> }>();

</script>

<template>
  <Head title="Pricing" />
  <SiteLayout>
    <!-- Hero -->
    <section class="relative bg-cover bg-center min-h-[60vh] flex items-center px-6 md:px-12" style="background-image: url('/hero-image.jpg');">
      <div class="absolute inset-0 bg-black/30 z-0"></div>
      <div class="relative z-10 container mx-auto grid grid-cols-1 md:grid-cols-5 gap-8 items-center">
        <div class="md:col-span-3 text-white space-y-6 md:pl-7">
          <h1 class="text-4xl sm:text-5xl font-extrabold">Fast, Reliable License & Insurance Verification API for Car Rentals</h1>
          <p class="text-lg max-w-xl">Verify renters’ driving licenses and insurance in seconds. Reduce risk, save time, and boost ROI with our scalable API solution.</p>
          <div class="mt-2">
            <a href="#pricing" class="px-6 py-3 bg-[var(--primary)] rounded-md hover:opacity-90 transition">Request API Access</a>
          </div>
        </div>
        <div class="md:col-span-2 hidden md:block"></div>
      </div>
    </section>

    <!-- Why Choose -->
    <section class="bg-background py-20 px-6 md:px-12">
      <div class="mx-auto max-w-screen text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-6">Why Choose InsureVerifyAI?</h2>
        <p class="text-foreground/70 max-w-2xl mx-auto mb-6">Smarter Verifications. Safer Rentals</p>
        <div class="inline-block bg-[var(--primary)] text-white px-6 py-4 rounded-lg shadow-md mb-8">
          <p class="text-2xl md:text-3xl font-extrabold leading-tight">$1,000,000</p>
          <p class="text-xs md:text-sm uppercase tracking-wider text-white/80">Fraud-Related Theft Coverage</p>
        </div>

        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-2 mt-10">
          <div class="flex flex-col items-center text-center space-y-4">
            <div class="bg-black rounded-full p-4 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-foreground">Identity & License Verification (AI + Biometrics)</h3>
              <p class="text-foreground/70">Scan government-issued IDs and biometrics to ensure the person matches the license.</p>
            </div>
          </div>

          <div class="flex flex-col items-center text-center space-y-4">
            <div class="bg-black rounded-full p-4 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-foreground">Real-Time Insurance Verification</h3>
              <p class="text-foreground/70">Auto-reads declarations pages to confirm active coverage instantly.</p>
            </div>
          </div>

          <div class="flex flex-col items-center text-center space-y-4">
            <div class="bg-black rounded-full p-4 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 18 6-6-6-6"></path><path d="m8 6-6 6 6 6"></path></svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-foreground">Recorded Insurance Confirmation</h3>
              <p class="text-foreground/70">Optional add-on to record verification calls for stronger protection.</p>
            </div>
          </div>

          <div class="flex flex-col items-center text-center space-y-4">
            <div class="bg-black rounded-full p-4 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v5"></path><path d="M16 14v7"></path><path d="M20 10v11"></path><path d="m22 3-8.646 8.646a.5.5 0 0 1-.708 0L9.354 8.354a.5.5 0 0 0-.707 0L2 15"></path><path d="M4 18v3"></path><path d="M8 14v7"></path></svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-foreground">Scalable Growth</h3>
              <p class="text-foreground/70">From small agencies to enterprise fleets—grow without extra overhead.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="bg-black text-white py-16 px-6 md:px-12">
      <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Approve More Renters in Seconds?</h2>
        <p class="text-gray-300 mb-8 max-w-2xl mx-auto">Start verifying driving licenses and insurance instantly with InsureVerifyAI.</p>
        <div class="flex justify-center flex-wrap gap-4">
          <a href="#pricing" class="bg-white text-black px-6 py-3 rounded-md font-medium hover:bg-gray-100 transition">Get Started</a>
        </div>
      </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="bg-background py-16 px-6 md:px-12">
      <div class="mx-auto max-w-screen text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">Pricing</h2>
        <p class="text-gray-700 mb-12 max-w-2xl mx-auto">Simple, transparent pricing that scales with your business</p>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <div
            v-for="plan in props.plans"
            :key="plan.id"
            class="border border-black p-8 rounded-lg flex flex-col justify-between text-left"
          >
            <div>
              <h3 class="text-xl font-semibold text-black mb-1">{{ plan.name }}</h3>
              <div class="flex flex-col items-start mb-2">
                <div class="flex items-baseline">
                  <span class="text-3xl font-bold text-black">${{ Number(plan.price).toFixed(0) }}</span>
                  <span class="text-sm text-gray-600 ml-2">/month</span>
                </div>
                <span class="text-sm text-gray-600 mt-1">Includes {{ plan.image_limit }} image uploads</span>
              </div>
              <p class="text-sm text-gray-700 mb-4">Choose a plan and start verifying instantly.</p>
              <ul class="text-gray-800 text-sm space-y-3">
                <li class="flex items-start"><span class="w-2 h-2 mt-2 mr-2 bg-black rounded-full"></span><span>No monthly minimum</span></li>
                <li class="flex items-start"><span class="w-2 h-2 mt-2 mr-2 bg-black rounded-full"></span><span>Access to all features</span></li>
                <li class="flex items-start"><span class="w-2 h-2 mt-2 mr-2 bg-black rounded-full"></span><span>Real-time checks</span></li>
                <li v-if="plan.anet_plan_id" class="flex items-start"><span class="w-2 h-2 mt-2 mr-2 bg-black rounded-full"></span><span>Authorize.Net plan code: {{ plan.anet_plan_id }}</span></li>
              </ul>
            </div>
            <Link
              :href="route('subscription.show', { plan: plan.id })"
              class="mt-8 inline-block bg-primary text-white py-2 px-4 rounded hover:opacity-90 transition text-center"
            >
              Subscribe
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- Partners -->
    <section class="bg-white text-[#0a0a08] py-20 px-6 md:px-12">
      <div class="max-w-screen mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
          <p class="text-sm uppercase tracking-wide text-[#0086ed] font-semibold">PARTNER. CLIENT. COMMUNITY.</p>
          <h2 class="text-4xl md:text-5xl font-extrabold mt-4 mb-6">We Work With the Best Partners</h2>
          <p class="text-gray-700 mb-8">We collaborate with leading companies and communities to deliver better, faster, and more innovative solutions.</p>
          <a href="#pricing" class="inline-block bg-[#0086ed] text-white px-6 py-3 rounded-md font-medium hover:bg-blue-600 transition">Read More</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
          <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
            <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
              <img alt="Partner 1" class="object-contain w-full h-full" src="/logo.svg" />
            </div>
            <span class="mt-3 text-sm font-medium text-center line-clamp-2">Partner One</span>
          </div>
          <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
            <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
              <img alt="Partner 2" class="object-contain w-full h-full" src="/logo.svg" />
            </div>
            <span class="mt-3 text-sm font-medium text-center line-clamp-2">Partner Two</span>
          </div>
          <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
            <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
              <img alt="Partner 3" class="object-contain w-full h-full" src="/logo.svg" />
            </div>
            <span class="mt-3 text-sm font-medium text-center line-clamp-2">Partner Three</span>
          </div>
          <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
            <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
              <img alt="Partner 4" class="object-contain w-full h-full" src="/logo.svg" />
            </div>
            <span class="mt-3 text-sm font-medium text-center line-clamp-2">Partner Four</span>
          </div>
          <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
            <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
              <img alt="Partner 5" class="object-contain w-full h-full" src="/logo.svg" />
            </div>
            <span class="mt-3 text-sm font-medium text-center line-clamp-2">Partner Five</span>
          </div>
          <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
            <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
              <img alt="Partner 6" class="object-contain w-full h-full" src="/logo.svg" />
            </div>
            <span class="mt-3 text-sm font-medium text-center line-clamp-2">Partner Six</span>
          </div>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>


