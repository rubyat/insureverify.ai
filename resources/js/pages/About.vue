<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="About Us" />
  <SiteLayout>
    <section class="py-16 px-6 md:px-12 max-w-4xl mx-auto space-y-6">
      <h1 class="text-3xl md:text-4xl font-bold">About InsureVerifyAI</h1>
      <p>InsureVerifyAI is a verification platform for car rental & mobility companies with 12+ years of rental industry experience.</p>
      <ul class="list-disc pl-6 text-foreground/80">
        <li>Partners with Gather and CLEAR</li>
        <li>Coverage up to <strong>$300,000</strong> in case of identity verification failure</li>
        <li>Verify licenses, check insurance, automate approvals, and integrate with your systems</li>
      </ul>
      <p class="text-foreground/70">We help you save costs, ensure compliance, reduce fraud, and avoid storing sensitive data longer than necessary.</p>
    </section>
  </SiteLayout>
</template>
