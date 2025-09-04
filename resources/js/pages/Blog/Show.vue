<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import SiteLayout from '@/layouts/SiteLayout.vue'
import BlockRenderer from '@/pages/Admin/Pages/components/BlockRenderer.vue'

defineOptions({ name: 'PublicPageShow' })

const props = defineProps<{
  blog: {
    id: number
    slug: string
    title?: string | null
    seo?: any
    category?: { id: number; title: string; slug: string } | null
    publish_date?: string | null
    author?: string | null
    content?: string | null
    image?: string | null
    tags?: string[] | null
  }
  template: Record<string, any>
  relatedBlogs: Array<{
    id: number
    title: string
    slug: string
    image?: string | null
    thumbnail?: string | null
  }>
  blogBanner?: string | null
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
    <meta property="og:type" content="article" />
    <meta v-if="props.seo?.image" property="og:image" :content="props.seo?.image" />
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="props.seo?.title || props.blog.title || props.blog.slug" />
    <meta v-if="props.seo?.description" name="twitter:description" :content="props.seo?.description" />
    <meta v-if="props.seo?.image" name="twitter:image" :content="props.seo?.image" />
  </Head>
  <SiteLayout>
    <section class="bg-white py-12 px-6 md:px-12">
      <div class="container mx-auto md:px-20">
        <div class="text-sm mb-8">
          <Link :href="route('home')">Home</Link>
          <span class="mx-2">/</span>
          <Link :href="route('blog.index')">Blog</Link>
          <span class="mx-2">/</span>
          <span class="text-gray-500">{{ blog.title }}</span>
        </div>

        <div class="text-center">
          <p class="text-sm text-gray-500 mb-2" v-if="blog.category">{{ blog.category.title }}</p>
          <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ blog.title }}</h1>
          <p v-if="blog.publish_date" class="text-sm text-gray-500">{{ new Date(blog.publish_date).toLocaleDateString('en-GB') }}</p>
        </div>

        <img v-if="blogBanner" :src="blogBanner" :alt="blog.title || ''" class="rounded-lg w-full h-auto object-cover my-8">

        <div class="prose prose-lg max-w-none mx-auto">
          <BlockRenderer :template="template" />
        </div>


        <div class="mt-8 pt-8 border-t">
          <div class="flex items-center mb-6">
            <div class="font-medium mr-4">Share</div>
            <div class="flex items-center space-x-2">
              <a :href="`https://www.facebook.com/sharer/sharer.php?u=${props.seo?.canonical || ''}&title=${props.seo?.title || ''}`" target="_blank" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200">
                <i class="fab fa-facebook"></i>
              </a>
              <a :href="`https://twitter.com/share?url=${props.seo?.canonical || ''}&text=${props.seo?.title || ''}`" target="_blank" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200">
                <i class="fab fa-linkedin"></i>
              </a>
            </div>
          </div>

          <div v-if="blog.tags && blog.tags.length" class="flex flex-wrap gap-2 mb-8">
            <Link v-for="tag in blog.tags" :key="tag" :href="route('blog.tag', tag)" class="bg-gray-100 text-gray-700 text-sm font-medium px-3 py-1 rounded-full hover:bg-gray-200">
              {{ tag }}
            </Link>
          </div>

          <div v-if="blog.author" class="flex items-center pt-8 border-t hidden">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gray-200 mr-4">
              <i class="fas fa-user text-gray-500 text-3xl"></i>
            </div>
            <div>
              <div class="font-bold">{{ blog.author }}</div>
              <div class="text-sm text-gray-500">Insure Verify AI</div>
            </div>
          </div>
        </div>

        <hr class="my-12">

        <div v-if="relatedBlogs.length" class="mt-12">
          <h2 class="text-2xl font-bold text-center mb-8">Related content</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="related in relatedBlogs" :key="related.id" class="border rounded-lg overflow-hidden">
              <Link :href="route('blog.show', related.slug)">
                <img :src="related.thumbnail || '/images/placeholder.png'" :alt="related.title" class="w-full h-40 object-cover">
                <div class="p-4">
                  <h3 class="font-semibold text-md">{{ related.title }}</h3>
                </div>
              </Link>
            </div>
          </div>
        </div>

      </div>
    </section>
  </SiteLayout>
</template>
