<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="API Docs" />
  <SiteLayout>
    <section class="py-16 px-6 md:px-12 max-w-4xl mx-auto space-y-6">
      <h1 class="text-3xl md:text-4xl font-bold">API Quick Start</h1>
      <pre class="bg-gray-900 text-gray-100 p-4 rounded overflow-x-auto"><code>POST https://api.insureverify.ai/v1/verify
Authorization: Bearer YOUR_API_KEY
Content-Type: application/json

{
  "license_number": "D123456789",
  "insurance_policy": "XYZ123456789"
}</code></pre>
      <h2 class="text-2xl font-semibold">Endpoints</h2>
      <ul class="list-disc pl-6">
        <li><code>/v1/verifylicense</code> – license status, validity, expiry</li>
        <li><code>/v1/verifyinsurance</code> – insurance provider, status, coverage date</li>
      </ul>
      <p class="text-foreground/70">Authentication: Bearer Token. Rate limits: Free 100/mo, Pro 10k/mo, Enterprise custom.</p>
      <p>Support: <a href="mailto:support@insureverify.ai" class="text-blue-600 underline">support@insureverify.ai</a></p>
    </section>
  </SiteLayout>
</template>
