<script setup lang="ts">
const props = defineProps<{ model: Record<string, any> }>()
</script>

<template>
  <section class="px-6 md:px-12" :class="[props.model.class, props.model.padding]">
    <!-- Unescaped on purpose to mimic current behavior; only use with trusted content -->
    <div class="container mx-auto">
        <div v-html="props.model.content || ''"></div>
    </div>
    <slot />
  </section>
</template>
