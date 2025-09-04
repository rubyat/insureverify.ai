<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import SeoForm from '@/components/SeoForm.vue'
import RichTextEditor from '@/components/RichTextEditor.vue'

const props = defineProps<{
  page?: any
  onSaved?: (payload: any) => void
}>()

const emit = defineEmits<{ (e: 'create'): void; (e: 'update'): void }>()

type PageFormPayload = {
  title: string
  slug: string
  status: number
  content: string
  seo: {
    seo_title: string
    seo_index: number | boolean
    seo_keyword: string
    seo_description: string
    seo_image: string | undefined
    canonical_url: string
    meta_json: Record<string, any>
  }
}

const form = useForm<PageFormPayload>({
  title: props.page?.title ?? '',
  slug: props.page?.slug ?? '',
  status: props.page?.status ?? 1,
  content: props.page?.content ?? '',
  seo: {
    seo_title: props.page?.seo?.seo_title ?? '',
    seo_index: props.page?.seo?.seo_index ?? 1,
    seo_keyword: props.page?.seo?.seo_keyword ?? '',
    seo_description: props.page?.seo?.seo_description ?? '',
    seo_image: props.page?.seo?.seo_image ?? undefined,
    canonical_url: props.page?.seo?.canonical_url ?? '',
    meta_json: props.page?.seo?.meta_json ?? {},
  },
})

// Transform payload to coerce IDs to integers or null
form.transform((data: any) => {
  return {
    ...data,
    seo: {
      ...data.seo,
      // cast boolean-like to 0/1 for backend if necessary
      seo_index: data.seo?.seo_index === true ? 1 : data.seo?.seo_index === false ? 0 : Number(data.seo?.seo_index ?? 1),
    },
  }
})

const submitting = ref(false)

// Simpler computed to avoid template expanding complex error mapped types
const hasErrors = computed(() => Object.keys((form.errors as unknown as Record<string, any>) || {}).length > 0)

// Slug auto-generation with manual override
const slugEditedManually = ref(false)
function slugify(input: string): string {
  return (input || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[^\w\s-]/g, '')
    .trim()
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '')
}
// Auto-generate when title changes unless user edited slug
watch(
  () => form.title,
  (v) => {
    if (!slugEditedManually.value) {
      form.slug = slugify(v || '')
    }
  }
)
function onSlugInput() {
  slugEditedManually.value = true
}
function regenerateSlug() {
  form.slug = slugify(form.title)
  slugEditedManually.value = false
}

function submitCreate() {
  submitting.value = true
  form.post(route('admin.pages.store'), {
    onFinish: () => (submitting.value = false),
    onSuccess: (page) => {
      props.onSaved?.(page)
      emit('create')
    },
    preserveScroll: true,
  })
}

function submitUpdate(id: number) {
  submitting.value = true
  form.put(route('admin.pages.update', id), {
    onFinish: () => (submitting.value = false),
    onSuccess: () => {
      emit('update')
    },
    preserveScroll: true,
  })
}



const quillContent = ref(form.content)
watch(quillContent, (v) => (form.content = v ?? ''))

// Placeholder for SEO image comes from backend (Seo::$appends['placeholder'])
const seoPlaceholder = computed(() => (props.page?.seo?.placeholder as string | undefined) ?? '/storage/placeholder.png')

</script>

<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main -->
    <div class="lg:col-span-2 space-y-6">
      <div v-if="hasErrors" class="rounded border border-red-200 bg-red-50 text-red-700 p-3">
        Please fix the highlighted fields and try again.
      </div>
      <div class="rounded border bg-white p-4 space-y-4">
        <div>
          <label class="block text-sm font-medium">Title</label>
          <input v-model="form.title" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Slug</label>
          <div class="flex gap-2 items-center">
            <input v-model="form.slug" @input="onSlugInput" type="text" class="mt-1 w-full rounded border px-3 py-2" />
            <button type="button" class="mt-1 px-3 py-2 rounded border" @click="regenerateSlug">Auto</button>
          </div>
          <div v-if="form.errors.slug" class="text-red-600 text-sm mt-1">{{ form.errors.slug }}</div>
        </div>
        <div class="hidden">
          <label class="block text-sm font-medium">Content (fallback)</label>
          <RichTextEditor v-model="form.content" />
        </div>
      </div>

      <SeoForm
        v-model="form.seo"
        :title="form.title"
        :slug="form.slug"
        :host="route('home')"
        :placeholder="seoPlaceholder"
        show-schema
      />
    </div>

    <!-- Side -->
    <div class="space-y-6">
      <div class="rounded border bg-white p-4 space-y-3">
        <div>
          <label class="block text-sm font-medium">Status</label>
          <select v-model.number="form.status" class="mt-1 w-full rounded border px-3 py-2">
            <option :value="1">Publish</option>
            <option :value="0">Draft</option>
          </select>
        </div>
        <div class="pt-2">
          <button v-if="!props.page" :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitCreate">Create</button>
          <button v-else :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitUpdate(props.page.id)">Save</button>
        </div>
      </div>
    </div>
  </div>

</template>

