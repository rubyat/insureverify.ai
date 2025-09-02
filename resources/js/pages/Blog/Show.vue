<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import SiteLayout from '@/layouts/SiteLayout.vue'
import BlockRenderer from '@/pages/Admin/Pages/components/BlockRenderer.vue'

defineOptions({ name: 'PublicPageShow' })

const props = defineProps<{
  blog: { id: number; slug: string; title?: string | null; seo?: any, category?: { id: number; title: string; slug: string } | null, publish_date?: string | null, author?: string | null, content?: string | null }
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
    <Head :title="props.seo?.title || props.blog.title || props.blog.slug">
    <meta v-if="props.seo?.description" name="description" :content="props.seo?.description" />
    <meta v-if="props.seo?.keywords" name="keywords" :content="props.seo?.keywords" />
    <link v-if="props.seo?.canonical" rel="canonical" :href="props.seo?.canonical" />
    <!-- Open Graph -->
    <meta property="og:title" :content="props.seo?.title || props.blog.title || props.blog.slug" />
    <meta v-if="props.seo?.description" property="og:description" :content="props.seo?.description" />
    <meta v-if="props.seo?.canonical" property="og:url" :content="props.seo?.canonical" />
    <meta property="og:type" content="website" />
    <meta v-if="props.seo?.image" property="og:image" :content="props.seo?.image" />
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="props.seo?.title || props.blog.title || props.blog.slug" />
    <meta v-if="props.seo?.description" name="twitter:description" :content="props.seo?.description" />
    <meta v-if="props.seo?.image" name="twitter:image" :content="props.seo?.image" />
  </Head>
  <SiteLayout>
    <section class="bg-white py-12 px-6 md:px-12">
      <div class="container mx-auto">
        <div class="text-sm text-foreground/60 mb-4 flex items-center gap-2">
          <Link :href="route('blog.index')" class="underline">Blog</Link>
          <span v-if="blog.category">/</span>
          <Link v-if="blog.category" :href="route('blog.category', blog.category.slug)" class="underline">
            {{ blog.category.title }}
          </Link>
        </div>

        <h1 class="text-3xl font-bold">{{ blog.title }}</h1>
        <div class="mt-2 text-sm text-foreground/60">
          <span v-if="blog.publish_date">{{ new Date(blog.publish_date).toLocaleDateString() }}</span>
          <span v-if="blog.author"> • By {{ blog.author }}</span>
        </div>

        <div class="prose prose-neutral max-w-none mt-8">
            <div class="min-h-[40vh]">
                <BlockRenderer :template="template" />
            </div>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
