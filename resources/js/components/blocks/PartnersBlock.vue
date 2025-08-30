<script setup lang="ts">
interface PartnerItem { name: string; logo: string; url: string }
const props = defineProps<{ model: {
  eyebrow?: string
  title?: string
  text?: string
  cta_label?: string
  cta_url?: string
  partners?: PartnerItem[]
} }>()

function isExternal(url?: string) {
  return !!url && /^https?:\/\//i.test(url)
}
</script>

<template>
  <!-- Partners -->
  <section class="bg-white text-[#0a0a08] py-20 px-12">
    <div class="container mx-auto grid md:grid-cols-2 gap-12 items-center">
      <!-- Left copy -->
      <div>
        <p v-if="props.model.eyebrow" class="text-sm uppercase tracking-wide text-[#0086ed] font-semibold">{{ props.model.eyebrow }}</p>
        <h2 class="text-4xl md:text-5xl font-extrabold mt-4 mb-6">{{ props.model.title }}</h2>
        <p class="text-gray-700 mb-8">{{ props.model.text }}</p>
        <a v-if="props.model.cta_url" :href="props.model.cta_url" class="inline-block bg-[#0086ed] text-white px-6 py-3 rounded-md font-medium hover:bg-blue-600 transition">{{ props.model.cta_label || 'Read More' }}</a>
      </div>

      <!-- Right logos grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
        <template v-for="(p, idx) in (props.model.partners || [])" :key="idx">
          <a :href="p.url" :target="isExternal(p.url) ? '_blank' : undefined" :rel="isExternal(p.url) ? 'noopener noreferrer' : undefined">
            <div class="flex flex-col items-center border border-gray-300 rounded-lg p-4 h-full">
              <div class="w-24 h-24 flex items-center justify-center overflow-hidden">
                <img :alt="p.name" class="object-contain w-full h-full" :src="p.logo" />
              </div>
              <span class="mt-3 text-sm font-medium text-center">{{ p.name }}</span>
            </div>
          </a>
        </template>
      </div>
    </div>
  </section>
</template>
