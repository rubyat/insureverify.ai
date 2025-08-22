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
    <section class="bg-background py-20 px-6 md:px-12">
      <div class="container mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold">Why Choose InsureVerifyAI?</h2>
        <p class="text-foreground/70 mt-2">Smarter Verifications. Safer Rentals</p>
        <div class="inline-block mt-8">
          <div class="bg-sky-600 text-white rounded-md shadow-lg ring-1 ring-sky-700/40 px-6 py-4">
            <p class="text-2xl md:text-3xl font-extrabold leading-tight">$3,000,000</p>
            <p class="text-[10px] md:text-xs uppercase tracking-wider text-white/90">Fraud-Related Theft Coverage</p>
          </div>
        </div>
      </div>

      <div class="container mx-auto max-w-5xl mt-16 grid md:grid-cols-2 gap-16">
        <!-- Identity & License Verification -->
        <div class="text-center">
          <div class="w-12 h-12 rounded-full bg-black text-white mx-auto flex items-center justify-center shadow">
            <i class="fa-solid fa-bolt text-lg"></i>
          </div>
          <h3 class="font-semibold mt-4">Identity & License Verification (Powered by AI + Biometric Tech)</h3>
          <p class="text-sm text-foreground/70 mt-2 max-w-md mx-auto">We verify each renter using the same system trusted by CLEAR at airports. It scans their government-issued ID and facial biometrics to ensure the license is real and belongs to the person renting. This step also includes $1 million liability coverage in case of any verification errors — providing you with maximum protection.</p>
        </div>

        <!-- Real-Time Insurance Verification -->
        <div class="text-center">
          <div class="w-12 h-12 rounded-full bg-black text-white mx-auto flex items-center justify-center shadow">
            <i class="fa-solid fa-shield text-lg"></i>
          </div>
          <h3 class="font-semibold mt-4">Real-Time Insurance Verification</h3>
          <p class="text-sm text-foreground/70 mt-2 max-w-md mx-auto">Our AI automatically reads the declarations page (deck page) of a customer’s insurance policy and checks whether the policy is active and valid. No more manual uploads or follow-ups — it’s verified instantly.</p>
        </div>

        <!-- Recorded Insurance Confirmation -->
        <div class="text-center">
          <div class="w-12 h-12 rounded-full bg-black text-white mx-auto flex items-center justify-center shadow">
            <i class="fa-solid fa-code text-lg"></i>
          </div>
          <h3 class="font-semibold mt-4">Bonus Add-On: Recorded Insurance Confirmation (Highly Recommended)</h3>
          <p class="text-sm text-foreground/70 mt-2 max-w-md mx-auto">As an optional add-on, this feature records the insurance verification process, providing you with a copy of the recording that confirms the renter’s coverage is active and applies to the rental vehicle’s actual cash value. It’s one of the most powerful tools available for backing up your coverage in the event of a claim.</p>
        </div>

        <!-- Scalable Growth -->
        <div class="text-center">
          <div class="w-12 h-12 rounded-full bg-black text-white mx-auto flex items-center justify-center shadow">
            <i class="fa-solid fa-chart-column text-lg"></i>
          </div>
          <h3 class="font-semibold mt-4">Scalable Growth</h3>
          <p class="text-sm text-foreground/70 mt-2 max-w-md mx-auto">From small rental agencies to enterprise-level fleets, our API grows with you. Handle more bookings without hiring more staff.</p>
        </div>
      </div>
    </section>

    <!-- CTA Banner -->
    <section class="bg-black text-white py-16 text-center px-6">
      <h2 class="text-3xl md:text-4xl font-bold">Ready to Approve More Renters in Seconds?</h2>
      <p class="text-gray-300 mt-2 max-w-2xl mx-auto">Start verifying driving licenses and insurance instantly.</p>
      <Link :href="route('signup')" class="btn-primary px-6 py-3 rounded-md inline-block mt-8">Get Started</Link>
    </section>

    <!-- Pricing -->
    <section class="bg-gray-100 py-20 px-6 md:px-12">
      <div class="container mx-auto">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold">Pricing</h2>
          <p class="text-foreground/70 mt-2">Simple, transparent pricing that scales with your business</p>
        </div>

        <div class="grid lg:grid-cols-5 gap-6 max-w-7xl mx-auto">
          <!-- Silver -->
          <div class="border rounded-lg p-6 flex flex-col">
            <h3 class="font-semibold text-lg">Silver</h3>
            <div class="mt-2 text-3xl font-extrabold tracking-tight">$99 <span class="text-base font-medium text-foreground/70">/month</span></div>
            <p class="text-sm text-foreground/70 mt-2">8 verifications included</p>
            <p class="text-sm mt-4 text-foreground/80">Perfect for startups, small teams, or companies just getting started.</p>
            <ul class="mt-6 space-y-3 text-sm text-foreground/80">
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> No monthly minimum</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Access to all verification features</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Real-time license and insurance checks</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Email and chat support</li>
            </ul>
            <div class="mt-auto pt-8">
              <Link :href="route('plans.index')" class="btn-primary w-full text-center px-4 py-3 rounded">Get Started</Link>
            </div>
          </div>

          <!-- Bronze -->
          <div class="border rounded-lg p-6 flex flex-col">
            <h3 class="font-semibold text-lg">Bronze</h3>
            <div class="mt-2 text-3xl font-extrabold tracking-tight">$199 <span class="text-base font-medium text-foreground/70">/month</span></div>
            <p class="text-sm text-foreground/70 mt-2">20 verifications included</p>
            <p class="text-sm mt-4 text-foreground/80">For growing car rental platforms or businesses with steady verification volume.</p>
            <ul class="mt-6 space-y-3 text-sm text-foreground/80">
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Priority API access</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Full audit trail and activity logs</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Dedicated account manager</li>
            </ul>
            <div class="mt-auto pt-8">
              <Link :href="route('plans.index')" class="btn-primary w-full text-center px-4 py-3 rounded">Get Started</Link>
            </div>
          </div>

          <!-- Gold -->
          <div class="border rounded-lg p-6 flex flex-col">
            <h3 class="font-semibold text-lg">Gold</h3>
            <div class="mt-2 text-3xl font-extrabold tracking-tight">$349 <span class="text-base font-medium text-foreground/70">/month</span></div>
            <p class="text-sm text-foreground/70 mt-2">40 verifications included</p>
            <p class="text-sm mt-4 text-foreground/80">Verify identity, license, and insurance in real-time with API integration.</p>
            <ul class="mt-6 space-y-3 text-sm text-foreground/80">
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Identity verification</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> License status validation</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Insurance coverage check</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Real-time API response</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Fraud prevention system</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Basic API analytics</li>
            </ul>
            <div class="mt-auto pt-8">
              <Link :href="route('plans.index')" class="btn-primary w-full text-center px-4 py-3 rounded">Get Started</Link>
            </div>
          </div>

          <!-- Platinum -->
          <div class="border rounded-lg p-6 flex flex-col">
            <h3 class="font-semibold text-lg">Platinum</h3>
            <div class="mt-2 text-3xl font-extrabold tracking-tight">$499 <span class="text-base font-medium text-foreground/70">/month</span></div>
            <p class="text-sm text-foreground/70 mt-2">75 verifications included</p>
            <p class="text-sm mt-4 text-foreground/80">Verify identity, license, insurance, and record a phone call with AI-backed voice confirmation for added security.</p>
            <ul class="mt-6 space-y-3 text-sm text-foreground/80">
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Identity verification</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> License & insurance checks</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> AI-enhanced voice confirmation</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Call recording with timestamps</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Advanced fraud prevention</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Customizable verification flow</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Priority API support</li>
            </ul>
            <div class="mt-auto pt-8">
              <Link :href="route('plans.index')" class="btn-primary w-full text-center px-4 py-3 rounded">Request Access</Link>
            </div>
          </div>

          <!-- Enterprise -->
          <div class="border rounded-lg p-6 flex flex-col">
            <h3 class="font-semibold text-lg">Enterprise</h3>
            <div class="mt-2 text-3xl font-extrabold tracking-tight">Custom Pricing</div>
            <p class="text-sm mt-4 text-foreground/80">Custom plans for high-volume fleets, platforms, or partners who need advanced features and support.</p>
            <ul class="mt-6 space-y-3 text-sm text-foreground/80">
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Volume-based discounts</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Custom SLAs</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> API usage insights and reporting</li>
              <li class="flex items-start gap-2"><span class="mt-1 w-2 h-2 bg-black rounded-full"></span> Premium support and onboarding</li>
            </ul>
            <div class="mt-auto pt-8">
              <Link :href="route('contact')" class="btn-primary w-full text-center px-4 py-3 rounded">Contact us</Link>
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
