<script setup lang="ts">
import { computed } from 'vue'
const props = defineProps<{ model: Record<string, any> }>()
const overlay = computed(() => {
  const v = parseFloat(props.model.overlay_opacity ?? '0.4')
  if (Number.isNaN(v)) return 0.4
  return Math.min(1, Math.max(0, v))
})
</script>

<template>
  <section
    class="relative h-[80vh] md:min-h-[95vh] md:max-h-[100vh] bg-cover bg-center flex items-center px-6 md:px-12"
    :style="{ backgroundImage: `url(${props.model.background_image})` }"
  >
    <div class="absolute inset-0" :style="{ backgroundColor: `rgba(0,0,0,${overlay})` }"></div>
    <div class="container mx-auto">
      <div class="relative z-10 max-w-6xl">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white max-w-3xl">{{ props.model.title }}</h1>
        <p class="text-white/90 text-lg max-w-2xl mt-4">{{ props.model.subtitle }}</p>
        <div class="mt-8 flex gap-3">
          <a :href="props.model.primary_url || '#'" class="btn-primary px-6 py-3 rounded-md">{{ props.model.primary_text || 'Primary' }}</a>
          <a :href="props.model.secondary_url || '#'" class="px-6 py-3 rounded-md bg-white text-black hover:bg-gray-100">{{ props.model.secondary_text || 'Secondary' }}</a>
        </div>
      </div>
    </div>
    <slot />
  </section>
</template>
