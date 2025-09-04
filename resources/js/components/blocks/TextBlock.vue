<script setup lang="ts">
const props = defineProps<{ model: Record<string, any> }>()
import '@vueup/vue-quill/dist/vue-quill.snow.css';
</script>

<template>
  <section class="md:px-12" :class="[props.model.class, props.model.padding]">
    <!-- Unescaped on purpose to mimic current behavior; only use with trusted content -->
    <div class="container mx-auto ql-snow">
        <div class="ql-editor" v-html="props.model.content || ''"></div>
    </div>
    <slot />
  </section>
</template>
