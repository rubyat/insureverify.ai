<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import BlockRenderer from '@/pages/Admin/Pages/components/BlockRenderer.vue'
import { Head } from '@inertiajs/vue3'

defineOptions({ name: 'PublicPageShow' })

const props = defineProps<{
  page: { id: number; slug: string; title?: string | null }
  template: Record<string, any>
  seo?: {
    title?: string | null
    description?: string | null
    keywords?: string | null
    canonical?: string | null
    image?: string | null
  }
}>()
</script>

<template>
  <Head :title="props.seo?.title || props.page.title || props.page.slug">
    <meta v-if="props.seo?.description" name="description" :content="props.seo?.description" />
    <meta v-if="props.seo?.keywords" name="keywords" :content="props.seo?.keywords" />
    <link v-if="props.seo?.canonical" rel="canonical" :href="props.seo?.canonical" />
    <!-- Open Graph -->
    <meta property="og:title" :content="props.seo?.title || props.page.title || props.page.slug" />
    <meta v-if="props.seo?.description" property="og:description" :content="props.seo?.description" />
    <meta v-if="props.seo?.canonical" property="og:url" :content="props.seo?.canonical" />
    <meta property="og:type" content="website" />
    <meta v-if="props.seo?.image" property="og:image" :content="props.seo?.image" />
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="props.seo?.title || props.page.title || props.page.slug" />
    <meta v-if="props.seo?.description" name="twitter:description" :content="props.seo?.description" />
    <meta v-if="props.seo?.image" name="twitter:image" :content="props.seo?.image" />
  </Head>
  <SiteLayout>
    <div class="min-h-[40vh]">
      <BlockRenderer :template="template" />
    </div>
  </SiteLayout>
</template>
