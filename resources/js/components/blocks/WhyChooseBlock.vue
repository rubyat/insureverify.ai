<script setup lang="ts">
const props = defineProps<{ model: Record<string, any> }>()
</script>

<template>
  <section class="bg-background py-20 px-6 md:px-12">
    <div class="container mx-auto text-center">
      <h2 class="text-3xl md:text-4xl font-bold">{{ props.model.title }}</h2>
      <p class="text-foreground/70 mt-2">{{ props.model.subtitle }}</p>
      <div class="inline-block mt-8">
        <div class="bg-sky-600 text-white rounded-md shadow-lg ring-1 ring-sky-700/40 px-6 py-4">
          <p class="text-2xl md:text-3xl font-extrabold leading-tight">{{ props.model.highlight_amount }}</p>
          <p class="text-[10px] md:text-xs uppercase tracking-wider text-white/90">{{ props.model.highlight_label }}</p>
        </div>
      </div>
    </div>

    <div class="container mx-auto mt-16 grid md:grid-cols-2 gap-16">
      <div v-for="(f, idx) in (props.model.features || [])" :key="idx" class="text-center">
        <div class="w-12 h-12 rounded-full bg-black text-white mx-auto flex items-center justify-center shadow">
          <i :class="[f.icon, 'text-lg']"></i>
        </div>
        <h3 class="font-semibold mt-4">{{ f.title }}</h3>
        <p class="text-sm text-foreground/70 mt-2 mx-auto">{{ f.text }}</p>
      </div>
    </div>
  </section>
</template>
