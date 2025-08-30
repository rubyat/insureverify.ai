<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import SiteLayout from '@/layouts/SiteLayout.vue'

const props = defineProps<{
  blog: {
    id: number
    title: string
    slug: string
    content?: string
    author?: string | null
    publish_date?: string | null
    image?: string | null
    category?: { id: number; title: string; slug: string } | null
    seo?: any
  }
}>()

const title = props.blog?.seo?.seo_title || props.blog?.title || 'Blog'
const description = props.blog?.seo?.seo_description || ''
</script>

<template>
  <Head :title="title">
    <meta v-if="description" name="description" :content="description" />
  </Head>
  <SiteLayout>
    <section class="bg-white py-12 px-6 md:px-12">
      <div class="container mx-auto max-w-3xl">
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

        <div class="prose prose-neutral max-w-none mt-8" v-html="blog.content"></div>
      </div>
    </section>
  </SiteLayout>
</template>
