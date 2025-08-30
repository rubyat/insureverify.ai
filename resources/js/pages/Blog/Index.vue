<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
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
  activeCategory?: string | null
  filters?: { q?: string | null }
}>()

const q = ref(props.filters?.q ?? '')

watch(q, (val) => {
  const params: Record<string, any> = {}
  if (val) params.q = val
  if (props.activeCategory) params.category = props.activeCategory
  router.get(route(props.activeCategory ? 'blog.category' : 'blog.index', props.activeCategory ?? undefined), params, {
    preserveState: true,
    replace: true,
  })
})

const pageTitle = computed(() => {
  return props.activeCategory ? `Blog – ${props.activeCategory.replace('-', ' ')}` : 'Blog'
})

function excerpt(html?: string, max = 160) {
  if (!html) return ''
  const text = decodeAndStrip(html)
  return text.length > max ? text.slice(0, max).trimEnd() + '…' : text
}

function goToCategory(slug?: string) {
  if (!slug) return router.get(route('blog.index'))
  router.get(route('blog.category', slug), { q: q.value || undefined }, { preserveState: true })
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
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
          <h1 class="text-3xl font-bold">Blog</h1>
          <input
            v-model="q"
            type="search"
            placeholder="Search posts…"
            class="w-full md:w-80 border rounded px-3 py-2"
          />
        </div>

        <div class="flex flex-col md:flex-row gap-8">
          <!-- Sidebar categories -->
          <aside class="md:w-64">
            <div class="mb-3 font-semibold text-sm text-foreground/70">Categories</div>
            <div class="flex flex-wrap md:flex-col gap-2">
              <button
                class="text-sm px-3 py-1 rounded border"
                :class="!activeCategory ? 'bg-black text-white border-black' : 'hover:bg-gray-50'"
                @click="goToCategory()"
              >All</button>
              <button
                v-for="c in categories"
                :key="c.id"
                class="text-sm px-3 py-1 rounded border text-left"
                :class="activeCategory === c.slug ? 'bg-black text-white border-black' : 'hover:bg-gray-50'"
                @click="goToCategory(c.slug)"
              >{{ c.title }}</button>
            </div>
          </aside>

          <!-- Posts grid -->
          <div class="flex-1">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <article
                v-for="b in blogs.data"
                :key="b.id"
                class="border rounded-lg p-5 bg-white hover:shadow-sm transition"
              >
                <div class="text-xs text-foreground/60 flex items-center gap-2 mb-2">
                  <span v-if="b.category">
                    <Link :href="route('blog.category', b.category.slug)" class="underline hover:no-underline">
                      {{ b.category.title }}
                    </Link>
                  </span>
                  <span v-if="b.publish_date">• {{ new Date(b.publish_date).toLocaleDateString() }}</span>
                </div>
                <h2 class="text-lg font-semibold">
                  <Link :href="route('blog.show', b.slug)" class="hover:underline">{{ b.title }}</Link>
                </h2>
                <p class="mt-2 text-sm text-foreground/70">{{ excerpt(b.content) }}</p>
                <div class="mt-4">
                  <Link :href="route('blog.show', b.slug)" class="text-sm underline">Read more</Link>
                </div>
              </article>
            </div>

            <!-- Pagination -->
            <nav v-if="blogs.links?.length" class="mt-10 flex justify-center">
              <ul class="inline-flex -space-x-px">
                <li v-for="link in blogs.links" :key="link.label">
                  <Link
                    v-if="link.url"
                    :href="link.url"
                    class="px-3 py-2 border text-sm"
                    :class="link.active ? 'bg-black text-white border-black' : 'hover:bg-gray-50'"
                  >{{ formatLabel(link.label) }}</Link>
                  <span v-else class="px-3 py-2 border text-sm text-foreground/50">{{ formatLabel(link.label) }}</span>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
