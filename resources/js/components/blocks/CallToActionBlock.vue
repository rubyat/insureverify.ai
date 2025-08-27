<script setup lang="ts">
const props = defineProps<{ model: Record<string, any> }>()
</script>

<template>
  <section class="cta" :style="{ padding: '2rem', background: props.model.bg_color || '#f7fafc' }">
    <div class="container mx-auto text-center">
      <h2 class="text-2xl font-semibold">{{ props.model.title || '' }}</h2>
      <p class="mt-2 opacity-80">{{ props.model.sub_title || '' }}</p>
      <a :href="props.model.link_more || '#'" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded">
        {{ props.model.link_title || 'Learn more' }}
      </a>
    </div>
    <slot />
  </section>
</template>
