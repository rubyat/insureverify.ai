<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import BlockRenderer from '@/pages/Admin/Pages/components/BlockRenderer.vue'

defineOptions({ name: 'PublicPageShow' })

const props = defineProps<{
  page: { id: number; slug: string; title?: string | null }
  template: Record<string, any>
}>()
</script>

<template>
  <SiteLayout>
    <div class="min-h-[40vh]">
      <BlockRenderer :template="template" />
    </div>
  </SiteLayout>
</template>
