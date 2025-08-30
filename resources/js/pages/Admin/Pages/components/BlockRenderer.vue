<script setup lang="ts">
import { defineAsyncComponent, computed } from 'vue'
defineOptions({ name: 'BlockRenderer' })

const props = defineProps<{
  template: Record<string, any>
  nodeId?: string
  selectable?: boolean
}>()
const emit = defineEmits<{ (e: 'select', id: string): void }>()

// Default node is ROOT
const nid = computed(() => props.nodeId ?? 'ROOT')

// Registry mapping block type -> Vue component
// You can expand this or convert to dynamic imports per block id
const registry: Record<string, any> = {
  root: defineAsyncComponent(() => import('@/components/blocks/RootBlock.vue')),
  text: defineAsyncComponent(() => import('@/components/blocks/TextBlock.vue')),
  call_to_action: defineAsyncComponent(() => import('@/components/blocks/CallToActionBlock.vue')),
  faq: defineAsyncComponent(() => import('@/components/blocks/FaqBlock.vue')),
  hero: defineAsyncComponent(() => import('@/components/blocks/HeroBlock.vue')),
  why_choose: defineAsyncComponent(() => import('@/components/blocks/WhyChooseBlock.vue')),
  cta_banner: defineAsyncComponent(() => import('@/components/blocks/CtaBannerBlock.vue')),
  content_section: defineAsyncComponent(() => import('@/components/blocks/ContentSectionBlock.vue')),
  pricing: defineAsyncComponent(() => import('@/components/blocks/PricingBlock.vue')),
  contact: defineAsyncComponent(() => import('@/components/blocks/ContactBlock.vue')),
  partners: defineAsyncComponent(() => import('@/components/blocks/PartnersBlock.vue')),
  features: defineAsyncComponent(() => import('@/components/blocks/FeaturesBlock.vue')),
}

function childrenOf(nodeId: string) {
  const node = props.template[nodeId]
  return (node?.nodes as string[] | undefined) ?? []
}

function resolve(type?: string) {
  return (type && registry[type]) || 'div'
}

function onClickSelf() {
  if (props.selectable) emit('select', nid.value)
}

</script>

<template>
  <component :is="resolve(template[nid]?.type)" v-bind="{ model: template[nid]?.model || {} }" @click.stop="onClickSelf">
    <template v-for="cid in childrenOf(nid)" :key="cid">
      <BlockRenderer :template="template" :node-id="cid" :selectable="props.selectable" @select="(id) => emit('select', id)" />
    </template>
  </component>
</template>
