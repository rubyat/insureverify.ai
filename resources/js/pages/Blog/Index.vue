<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import SiteLayout from '@/layouts/SiteLayout.vue'
import { decodeAndStrip } from '@/utils/strings'

interface Category { id: number; title: string; slug: string }
interface BlogItem {
  id: number
  title: string
  slug: string
  content?: string
  author?: string
  publish_date?: string
  image?: string | null
  thumbnail?: string | null
  category?: { id: number; title: string; slug: string } | null
}

const props = defineProps<{
  blogs: {
    data: BlogItem[]
    links: Array<{ url: string | null; label: string; active: boolean }>
    next_page_url?: string | null
    prev_page_url?: string | null
  }
  categories: Category[]
  tags: string[]
  activeCategory?: string | null
  activeTag?: string | null
  filters?: { q?: string | null }
}>()

const pageTitle = computed(() => {
  let title = 'Blog'
  if (props.activeCategory) {
    title += ` – ${props.activeCategory.replace('-', ' ')}`
  } else if (props.activeTag) {
    title += ` – ${props.activeTag}`
  }
  return title
})

function excerpt(html?: string, max = 160) {
  if (!html) return ''
  const text = decodeAndStrip(html)
  return text.length > max ? text.slice(0, max).trimEnd() + '…' : text
}

function formatLabel(label: string) {
  return decodeAndStrip(label)
}
</script>

<template>
  <Head :title="pageTitle">
    <meta name="description" content="Insights, updates, and guides from InsureVerify AI" />
  </Head>
  <SiteLayout>
    <section class="bg-white py-12 px-6 md:px-12">
      <div class="container mx-auto">
        <div class="text-sm mb-8">
          <Link :href="route('home')">Home</Link>
          <span class="mx-2">/</span>
          <span>Blog</span>
        </div>

        <h1 class="text-3xl font-bold text-center mb-12">Blog</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
          <!-- Main Content -->
          <div class="lg:col-span-2">
            <div class="space-y-12">
              <article v-for="b in blogs.data" :key="b.id" class="flex flex-col md:flex-row gap-6 items-start">
                <Link :href="route('blog.show', b.slug)" class="w-full md:w-1/3 flex-shrink-0">
                  <img :src="b.thumbnail || '/images/placeholder.png'" :alt="b.title" class="rounded-lg w-full h-auto object-cover">
                </Link>
                <div class="w-full md:w-2/3">
                  <p class="text-sm text-gray-500 mb-2">{{ new Date(b.publish_date).toLocaleDateString('en-GB') }}</p>
                  <h2 class="text-xl font-semibold mb-2">
                    <Link :href="route('blog.show', b.slug)" class="hover:text-blue-600 transition-colors">{{ b.title }}</Link>
                  </h2>
                  <p class="text-gray-600">{{ b.content }}</p>
                </div>
              </article>
            </div>

            <!-- Pagination -->
            <nav v-if="blogs.links?.length > 3" class="mt-10 flex justify-center">
              <ul class="inline-flex -space-x-px">
                <li v-for="link in blogs.links" :key="link.label">
                  <Link
                    v-if="link.url"
                    :href="link.url"
                    class="px-3 py-2 border text-sm"
                    :class="link.active ? 'bg-black text-white border-black' : 'hover:bg-gray-50'"
                  >{{ formatLabel(link.label) }}</Link>
                  <span v-else class="px-3 py-2 border text-sm text-gray-400">{{ formatLabel(link.label) }}</span>
                </li>
              </ul>
            </nav>
          </div>

          <!-- Sidebar -->
          <aside class="lg:col-span-1 space-y-8">
            <div class="p-6 border rounded-lg">
              <h3 class="font-bold text-lg mb-4">Categories</h3>
              <ul class="space-y-2">
                <li>
                  <Link :href="route('blog.index')" class="text-sm hover:text-blue-600 transition-colors" :class="{'font-bold text-blue-600': !activeCategory && !activeTag}">All</Link>
                </li>
                <li v-for="c in categories" :key="c.id">
                  <Link :href="route('blog.category', c.slug)" class="text-sm hover:text-blue-600 transition-colors" :class="{'font-bold text-blue-600': activeCategory === c.slug}">{{ c.title }}</Link>
                </li>
              </ul>
            </div>
            <div v-if="tags.length" class="p-6 border rounded-lg">
              <h3 class="font-bold text-lg mb-4">Tags</h3>
              <div class="flex flex-wrap gap-2">
                <Link v-for="tag in tags" :key="tag" :href="route('blog.tag', tag)" class="text-xs px-2 py-1 rounded-full border transition-colors" :class="activeTag === tag ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-100'">
                  {{ tag }}
                </Link>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
