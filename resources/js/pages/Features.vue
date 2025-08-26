<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Zap, Code2, ShieldCheck, Server } from 'lucide-vue-next'
</script>

<template>
  <Head title="Features" />
  <SiteLayout>
    <section class="py-16 px-6 md:px-12 max-w-6xl mx-auto">
      <h1 class="text-3xl md:text-4xl font-bold text-center">All the Features You Need</h1>
      <p class="text-center text-foreground/70 mt-2">From performance to security and support.</p>
      <div class="grid md:grid-cols-2 gap-6 lg:gap-8 mt-12">
        <div class="group rounded-xl border bg-background/50 p-6 hover:shadow-md transition">
          <div class="flex items-start gap-4">
            <div class="shrink-0 rounded-lg bg-primary/10 text-primary p-3 ring-1 ring-primary/20 transition-transform group-hover:-translate-y-0.5">
              <Zap class="w-6 h-6" />
            </div>
            <div class="space-y-1">
              <h3 class="font-semibold text-lg">Fast Verification</h3>
              <p class="text-sm text-foreground/70">Instant license and insurance checks with high accuracy.</p>
            </div>
          </div>
        </div>

        <div class="group rounded-xl border bg-background/50 p-6 hover:shadow-md transition">
          <div class="flex items-start gap-4">
            <div class="shrink-0 rounded-lg bg-primary/10 text-primary p-3 ring-1 ring-primary/20 transition-transform group-hover:-translate-y-0.5">
              <Code2 class="w-6 h-6" />
            </div>
            <div class="space-y-1">
              <h3 class="font-semibold text-lg">Developer Friendly</h3>
              <p class="text-sm text-foreground/70">Clean REST APIs, sandbox, and test data.</p>
            </div>
          </div>
        </div>

        <div class="group rounded-xl border bg-background/50 p-6 hover:shadow-md transition">
          <div class="flex items-start gap-4">
            <div class="shrink-0 rounded-lg bg-primary/10 text-primary p-3 ring-1 ring-primary/20 transition-transform group-hover:-translate-y-0.5">
              <ShieldCheck class="w-6 h-6" />
            </div>
            <div class="space-y-1">
              <h3 class="font-semibold text-lg">Secure & Encrypted</h3>
              <p class="text-sm text-foreground/70">Encryption in transit and at rest. Principle of least privilege.</p>
            </div>
          </div>
        </div>

        <div class="group rounded-xl border bg-background/50 p-6 hover:shadow-md transition">
          <div class="flex items-start gap-4">
            <div class="shrink-0 rounded-lg bg-primary/10 text-primary p-3 ring-1 ring-primary/20 transition-transform group-hover:-translate-y-0.5">
              <Server class="w-6 h-6" />
            </div>
            <div class="space-y-1">
              <h3 class="font-semibold text-lg">Scalable Infrastructure</h3>
              <p class="text-sm text-foreground/70">Scale from startups to enterprise fleets.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-12">
        <Link :href="route('signup')" class="btn-primary px-6 py-3 rounded-md">Request API Access</Link>
      </div>
    </section>
  </SiteLayout>
</template>
