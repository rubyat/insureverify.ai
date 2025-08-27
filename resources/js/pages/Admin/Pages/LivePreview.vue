<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import BlockRenderer from './components/BlockRenderer.vue'

const props = defineProps<{ page: any; template: Record<string, any> }>()
</script>

<template>
  <SiteLayout>
    <BlockRenderer :template="props.template" />
  </SiteLayout>
</template>
