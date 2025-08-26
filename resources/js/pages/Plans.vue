<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/SiteLayout.vue';

const props = defineProps<{ plans: Array<any> }>();

// SEO meta for Plans page
const title = 'Pricing – InsureVerifyAI';
const description = 'Simple, transparent pricing for InsureVerifyAI’s license & insurance verification API. Scale confidently with plans that fit your business.';
const keywords = 'Pricing,Plans,License verification,Insurance verification,API,InsureVerifyAI';

</script>

<template>
  <Head :title="title">
    <meta name="description" :content="description" />
    <meta name="keywords" :content="keywords" />
  </Head>
  <SiteLayout>




     <!-- Pricing -->
     <section class="bg-gray-100 py-20 px-6 md:px-12">
      <div class="container mx-auto">
        <div class="text-center mb-12">
          <h1 class="text-3xl md:text-4xl font-bold">Pricing</h1>
          <p class="text-foreground/70 mt-2">Simple, transparent pricing that scales with your business</p>
        </div>

        <div class="grid lg:grid-cols-5 gap-6 mx-auto">
          <div
            v-for="plan in props.plans ?? []"
            :key="plan.id"
            class="border rounded-lg p-6 flex flex-col bg-white transition-all duration-300 ease-out transform hover:-translate-y-1 hover:scale-[1.05] hover:shadow-xl hover:shadow-sky-900/10 hover:border-sky-200/70"
          >
            <h2 class="font-semibold text-lg">{{ plan.name }}</h2>
            <div class="mt-2 text-3xl font-extrabold tracking-tight">
              <template v-if="plan.price !== null && plan.price !== undefined">
                ${{ Number(plan.price).toFixed(0) }} <span class="text-base font-medium text-foreground/70">/month</span>
              </template>
              <template v-else>
                Custom Pricing
              </template>
            </div>
            <p v-if="plan.verifications_included" class="text-sm text-foreground/70 mt-2">{{ plan.verifications_included }} verifications included</p>
            <p v-if="plan.description" class="text-sm mt-4 text-foreground/80">{{ plan.description }}</p>
            <ul class="mt-6 space-y-3 text-sm text-foreground/80">
              <li v-for="(feat, idx) in (plan.features || [])" :key="idx" class="flex items-start gap-2">
                <span class="mt-1 w-2 h-2 bg-black rounded-full"></span>
                <span>{{ feat }}</span>
              </li>
            </ul>
            <div class="mt-auto pt-8">
              <Link :href="route('plan.show', plan.slug)" class="btn-primary w-full text-center px-4 py-3 rounded">{{ plan.cta_label || 'Get Started' }}</Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Partners -->
    <section class="bg-white text-[#0a0a08] py-20 px-12">
      <div class="max-w-screen mx-auto grid md:grid-cols-2 gap-12 items-center">
        <!-- Left copy -->
        <div>
          <p class="text-sm uppercase tracking-wide text-[#0086ed] font-semibold">PARTNER. CLIENT. COMMUNITY.</p>
          <h2 class="text-4xl md:text-5xl font-extrabold mt-4 mb-6">We Work With the Best Partners</h2>
          <p class="text-gray-700 mb-8">We collaborate with leading companies, organizations, and communities that share our values. These partnerships help us deliver better, faster, and more innovative solutions for everyone we serve.</p>
          <a href="/about-us" class="inline-block bg-[#0086ed] text-white px-6 py-3 rounded-md font-medium hover:bg-blue-600 transition">Read More</a>
        </div>

        <!-- Right logos grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
          <a target="_blank" rel="noopener noreferrer" href="https://fyvco.com/">
            <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
              <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
                <img alt="RentFYV" class="object-contain w-full h-full" src="/images/partner/RentFIV.jpg" />
              </div>
              <span class="mt-3 text-sm font-medium text-center">RentFYV</span>
            </div>
          </a>

          <a target="_blank" rel="noopener noreferrer" href="https://pandaexoticcarrental.com/">
            <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
              <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
                <img alt="PANDA EXOTIC CAR RENTALS" class="object-contain w-full h-full" src="/images/partner/panda.jpg" />
              </div>
              <span class="mt-3 text-sm font-medium text-center">PANDA EXOTIC CAR RENTALS</span>
            </div>
          </a>

          <a target="_blank" rel="noopener noreferrer" href="https://premierautolosangeles.com/">
            <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
              <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
                <img alt="Premier Auto Rental" class="object-contain w-full h-full" src="/images/partner/PremierAutoRental.jpg" />
              </div>
              <span class="mt-3 text-sm font-medium text-center">Premier Auto Rental</span>
            </div>
          </a>

          <a target="_blank" rel="noopener noreferrer" href="https://studinovskiexotics.com/">
            <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
              <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
                <img alt="Studinovski" class="object-contain w-full h-full" src="/images/partner/Studinovski.jpg" />
              </div>
              <span class="mt-3 text-sm font-medium text-center">Studinovski</span>
            </div>
          </a>

          <a target="_blank" rel="noopener noreferrer" href="https://fyvexoticcarrental.com/">
            <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
              <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
                <img alt="FYV EXOTIC CAR RENTAL" class="object-contain w-full h-full" src="/images/partner/FYVEXOTICCARRENTAL.jpg" />
              </div>
              <span class="mt-3 text-sm font-medium text-center">FYV EXOTIC CAR RENTAL</span>
            </div>
          </a>

          <a target="_blank" rel="noopener noreferrer" href="https://gather.technology/">
            <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
              <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
                <img alt="Gather" class="object-contain w-full h-full" src="/images/partner/Gather.jpg" />
              </div>
              <span class="mt-3 text-sm font-medium text-center">Gather</span>
            </div>
          </a>
        </div>
      </div>
    </section>


  </SiteLayout>
</template>


