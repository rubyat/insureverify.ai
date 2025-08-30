<script setup lang="ts">
interface FeatureItem { icon: string; title: string; text: string }
const props = defineProps<{ model: {
  title?: string
  subtitle?: string
  items?: FeatureItem[]
  cta_label?: string
  cta_url?: string
} }>()

function isExternal(url?: string) {
  return !!url && /^https?:\/\//i.test(url)
}
</script>

<template>
  <section class="py-16 px-6 md:px-12 max-w-6xl mx-auto">
    <h2 v-if="props.model.title" class="text-3xl md:text-4xl font-bold text-center">{{ props.model.title }}</h2>
    <p v-if="props.model.subtitle" class="text-center text-foreground/70 mt-2">{{ props.model.subtitle }}</p>

    <div class="grid md:grid-cols-2 gap-6 lg:gap-8 mt-12">
      <template v-for="(f, idx) in (props.model.items || [])" :key="idx">
        <div class="group rounded-xl border bg-background/50 p-6 hover:shadow-md transition">
          <div class="flex items-start gap-4">
            <div class="shrink-0 rounded-lg bg-primary/10 text-primary p-3 ring-1 ring-primary/20 transition-transform group-hover:-translate-y-0.5">
              <i :class="[f.icon, 'w-6 h-6 text-xl']"></i>
            </div>
            <div class="space-y-1">
              <h3 class="font-semibold text-lg">{{ f.title }}</h3>
              <p class="text-sm text-foreground/70">{{ f.text }}</p>
            </div>
          </div>
        </div>
      </template>
    </div>

    <div v-if="props.model.cta_url" class="text-center mt-12">
      <a :href="props.model.cta_url"
         :target="isExternal(props.model.cta_url) ? '_blank' : undefined"
         :rel="isExternal(props.model.cta_url) ? 'noopener noreferrer' : undefined"
         class="btn-primary px-6 py-3 rounded-md">
        {{ props.model.cta_label || 'Learn More' }}
      </a>
    </div>
  </section>
</template>
