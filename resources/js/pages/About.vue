<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="About Us" />
  <SiteLayout>
    <!-- Hero-like About section (two-column) -->
    <section class="py-16 px-6 md:px-12 bg-white">
      <div class="container mx-auto grid md:grid-cols-2 gap-12 items-center">
        <!-- Left: Text from the three paragraphs -->
        <div>
          <div class="mt-3 space-y-4">
            <h1 class="text-3xl md:text-5xl font-extrabold leading-tight text-slate-900">
                About Us
            </h1>
            <p class="text-base md:text-lg text-slate-600">
                <strong>InsureVerifyAI </strong>is a verification service purpose-built for car rental businesses, fleet managers, and mobility platforms. With over 12 years of experience in the rental industry, we understand the importance of protecting your assets while streamlining the customer onboarding process.
              Our platform provides a fast, secure, and reliable way to verify driving licenses and car insurance details before handing over a vehicle. We help companies make smarter, safer decisions by delivering accurate, real-time data from trusted sources—all through a powerful API that reduces manual checks, minimizes fraud risk, and speeds up the rental process.
            </p>
            <p class="text-base md:text-lg text-slate-600">
              We are proud to partner with <strong>Gather</strong>, who has a strategic alliance with CLEAR, a leader in secure identity verification. Through this partnership, we offer an exclusive insurance policy that protects you if an identity-only verification fails. In the rare event of such a failure, you’re covered for up to <strong>$300,000</strong> in losses.
            </p>
          </div>
          <div class="mt-8">
            <a :href="route('signup')" class="inline-flex items-center justify-center px-6 py-3 rounded-md bg-sky-600 text-white font-medium shadow-sm hover:bg-sky-700 transition">Sign Up for Free</a>
          </div>
        </div>

        <!-- Right: Image with decorative shapes -->
        <div class="relative">
          <!-- Main image -->
          <div class="relative ml-6 md:ml-10">
            <img src="/images/about.png" alt="About InsureVerifyAI" class="relative z-10 rounded-2xl shadow-xl" />
          </div>
        </div>
      </div>
    </section>

    <section class="py-16 px-6 md:px-12 bg-white">
      <div class="container mx-auto grid md:grid-cols-2 gap-12 items-center">
        <!-- Left: Image -->
        <div class="relative order-1 md:order-none">
          <div class="relative">
            <img src="/images/about-2.png" alt="About InsureVerifyAI" class="rounded-2xl shadow-xl" />
          </div>
        </div>

        <!-- Right: Text content (What We Do) -->
        <div class="mt-3 space-y-4">
          <h2 class="text-3xl md:text-5xl font-extrabold leading-tight text-slate-900">What We Do</h2>
          <p class="text-base md:text-lg text-slate-600">
            With <strong>InsureVerifyAI</strong>, you can:
          </p>
          <ul class="space-y-2 text-slate-700">
            <li class="flex items-start gap-2">
              <span class="mt-2 w-2 h-2 bg-black rounded-full"></span>
              <span>Instantly confirm if a renter holds a valid driver’s license</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-2 w-2 h-2 bg-black rounded-full"></span>
              <span>Check the current status of their insurance in seconds</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-2 w-2 h-2 bg-black rounded-full"></span>
              <span>Automate approval without manual review</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-2 w-2 h-2 bg-black rounded-full"></span>
              <span>Seamlessly integrate verification into your booking or onboarding system</span>
            </li>
          </ul>
          <p class="text-base md:text-lg text-slate-600">
            Whether you’re operating a small rental location or managing a high-volume mobility platform, our API equips you to verify drivers quickly, accurately, and securely.
          </p>
        </div>
      </div>
    </section>


    <!-- Rest of the about content -->
    <section class="py-8 px-6 md:px-12 bg-white">
      <div class="container mx-auto prose prose-lg max-w-screen mb-16">
        <div class="mt-3 space-y-4">
          <h2 class="text-3xl md:text-5xl font-extrabold leading-tight text-slate-900">Why It Matters</h2>
          <p class="text-base md:text-lg text-slate-600">Manual checks are slow, error-prone, and costly. With <strong>InsureVerifyAI</strong>, verification is fully automated—you send the request, and results return in seconds. This helps you onboard more customers, reduce fraud risks, and ensure that only qualified drivers get behind the wheel.</p>
          <p class="text-base md:text-lg text-slate-600">We also understand the critical role of compliance. That’s why we adhere to strict data protection standards and avoid storing sensitive information longer than necessary.</p>
          <p class="text-base md:text-lg text-slate-600">With our experience, technology, and trusted partnerships, we make rental verification faster, safer, and more reliable than ever.</p>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
