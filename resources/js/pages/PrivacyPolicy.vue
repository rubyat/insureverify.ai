<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Privacy Policy" />
  <SiteLayout>
    <section class="py-16 px-6 md:px-12 max-w-4xl mx-auto space-y-4">
      <h1 class="text-3xl md:text-4xl font-bold">Privacy Policy</h1>
      <p class="text-sm text-foreground/70">Effective Date: 01-08-2025</p>
      <p>This policy applies to the website, API services, support, and integrations.</p>
      <h2 class="text-2xl font-semibold mt-6">Information Collected</h2>
      <ul class="list-disc pl-6">
        <li>Customer/account info: Business name, contacts, API keys, billing, interactions.</li>
        <li>End-user data (verification subjects): Name, DOB, license info, insurance details.</li>
      </ul>
      <p class="text-foreground/70">We only process lawful use cases and avoid collecting data directly from consumers where not necessary.</p>
    </section>
  </SiteLayout>
</template>
