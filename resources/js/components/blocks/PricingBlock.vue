<script setup lang="ts">
const props = defineProps<{ model: Record<string, any> }>()
</script>

<template>
  <section class="bg-gray-100 py-20 px-6 md:px-12">
    <div class="container mx-auto">
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold">{{ props.model.title || 'Pricing' }}</h2>
        <p class="text-foreground/70 mt-2">{{ props.model.subtitle || '' }}</p>
      </div>

      <div class="grid lg:grid-cols-5 gap-6 mx-auto">
        <div
          v-for="plan in (props.model.plans || [])"
          :key="plan.id"
          class="border rounded-lg p-6 flex flex-col bg-white transition-all duration-300 ease-out transform hover:-translate-y-1 hover:scale-[1.05] hover:shadow-xl hover:shadow-sky-900/10 hover:border-sky-200/70"
        >
          <h3 class="font-semibold text-lg">{{ plan.name }}</h3>
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
            <a :href="route('plan.show', plan.slug)" class="btn-primary w-full text-center px-4 py-3 rounded">{{ plan.cta_label || 'Get Started' }}</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
