<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
const props = defineProps<{ model: Record<string, any> }>()
</script>

<template>
  <section class="text-center py-16 px-6" :style="{ backgroundColor: props.model.background_color || '#000', color: props.model.text_color || '#fff' }">
    <h2 class="text-3xl md:text-4xl font-bold">{{ props.model.title }}</h2>
    <p class="opacity-80 mt-2 max-w-2xl mx-auto">{{ props.model.subtitle }}</p>
    <Link :href="props.model.button_url || '#'" class="btn-primary px-6 py-3 rounded-md inline-block mt-8">{{ props.model.button_text || 'Get Started' }}</Link>
  </section>
</template>
