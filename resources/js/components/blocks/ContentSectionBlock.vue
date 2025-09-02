<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ model: Record<string, any> }>()

const hasImage = computed(() => !!(props.model.image))
const layout = computed(() => props.model.layout || 'image_right')
const isTextFull = computed(() => layout.value === 'text_full' || !hasImage.value)
const imageOnLeft = computed(() => layout.value === 'image_left' && hasImage.value)

const imgSrc = computed(() => props.model.image || '')
</script>

<template>
  <section class="py-16 px-6 md:px-12 bg-white">
    <div
      class="container mx-auto grid gap-12 items-center"
      :class="isTextFull ? 'grid-cols-1' : 'md:grid-cols-2'"
    >
      <!-- Image -->
      <div v-if="hasImage" :class="[ imageOnLeft ? 'md:order-first' : 'md:order-last', isTextFull ? 'col-span-1' : '' ]">
        <div class="relative">
          <img
            :src="imgSrc"
            :alt="props.model.image_alt || props.model.title || 'Section image'"
            class="rounded-2xl shadow-xl"
          />
        </div>
      </div>

      <!-- Text Content -->
      <div :class="isTextFull ? 'col-span-1' : ''">
        <div class="mt-3 space-y-4">
          <h2 v-if="props.model.title" class="text-3xl md:text-5xl font-extrabold leading-tight text-slate-900">
            {{ props.model.title }}
          </h2>
          <div class="prose prose-slate max-w-none" v-html="props.model.content"></div>
          <div v-if="props.model.cta_text && props.model.cta_url" class="mt-6">
            <a :href="props.model.cta_url" class="inline-flex items-center justify-center px-6 py-3 rounded-md bg-sky-600 text-white font-medium shadow-sm hover:bg-sky-700 transition">{{ props.model.cta_text }}</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
