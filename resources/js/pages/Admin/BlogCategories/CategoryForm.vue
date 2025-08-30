<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import ImagePicker from '@/components/filemanager/ImagePicker.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const props = defineProps<{
  category?: any
  onSaved?: (payload: any) => void
}>()

const emit = defineEmits<{ (e: 'create'): void; (e: 'update'): void }>()

type CategoryFormPayload = {
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

const form = useForm<CategoryFormPayload>({
  title: props.category?.title ?? '',
  slug: props.category?.slug ?? '',
  status: props.category?.status ?? 1,
  content: props.category?.content ?? '',
  seo: {
    seo_title: props.category?.seo?.seo_title ?? '',
    seo_index: props.category?.seo?.seo_index ?? 1,
    seo_keyword: props.category?.seo?.seo_keyword ?? '',
    seo_description: props.category?.seo?.seo_description ?? '',
    seo_image: props.category?.seo?.seo_image ?? undefined,
    canonical_url: props.category?.seo?.canonical_url ?? '',
    meta_json: props.category?.seo?.meta_json ?? {},
  },
})

form.transform((data: any) => ({
  ...data,
  seo: {
    ...data.seo,
    seo_index: data.seo?.seo_index === true ? 1 : data.seo?.seo_index === false ? 0 : Number(data.seo?.seo_index ?? 1),
  },
}))

const submitting = ref(false)
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
  form.post(route('admin.blog-categories.store'), {
    onFinish: () => (submitting.value = false),
    onSuccess: (payload) => {
      props.onSaved?.(payload)
      emit('create')
    },
    preserveScroll: true,
  })
}

function submitUpdate(id: number) {
  submitting.value = true
  form.put(route('admin.blog-categories.update', id), {
    onFinish: () => (submitting.value = false),
    onSuccess: () => emit('update'),
    preserveScroll: true,
  })
}

const quillContent = ref(form.content)
watch(quillContent, (v) => (form.content = v ?? ''))

// SEO
const seoModalOpen = ref(false)
const seoActiveTab = ref<'general' | 'facebook' | 'twitter'>('general')
if (!form.seo.meta_json) form.seo.meta_json = {}
if (!form.seo.meta_json.facebook) form.seo.meta_json.facebook = {}
if (!form.seo.meta_json.twitter) form.seo.meta_json.twitter = {}
const seoPlaceholder = computed(() => (props.category?.seo?.placeholder as string | undefined) ?? '/storage/placeholder.png')
const fb = computed({
  get: () => form.seo.meta_json.facebook,
  set: (v: any) => (form.seo.meta_json = { ...form.seo.meta_json, facebook: v }),
})
const tw = computed({
  get: () => form.seo.meta_json.twitter,
  set: (v: any) => (form.seo.meta_json = { ...form.seo.meta_json, twitter: v }),
})
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
        <div>
          <label class="block text-sm font-medium">Content</label>
          <QuillEditor v-model:content="quillContent" contentType="html" theme="snow" class="mt-1 bg-white" />
        </div>
      </div>

      <div class="rounded border bg-white p-4 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-medium">SEO</h3>
          <button type="button" class="text-blue-600 hover:underline" @click="seoModalOpen = true">Edit</button>
        </div>
        <div class="border rounded p-3">
          <div class="text-xs text-gray-500">Search engine</div>
          <div class="mt-2">
            <div class="text-sm text-gray-600 truncate">{{ (props.category ? route('home') + '/blog/category/' + form.slug : '') }}</div>
            <div class="text-xl text-blue-700 leading-tight">{{ form.seo.seo_title || form.title }}</div>
            <div class="text-gray-700">{{ form.seo.seo_description }}</div>
          </div>
        </div>
      </div>
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
          <button v-if="!props.category" :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitCreate">Create</button>
          <button v-else :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitUpdate(props.category.id)">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- SEO Modal -->
  <div v-if="seoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50" @click="seoModalOpen = false" />
    <div class="relative bg-white w-full max-w-3xl rounded shadow-lg">
      <div class="flex items-center justify-between px-4 py-3 border-b">
        <div class="font-medium">Search Engine</div>
        <button class="text-gray-500 hover:text-gray-800" @click="seoModalOpen = false">✕</button>
      </div>
      <div class="px-4 pt-3">
        <div class="mb-4">
          <label class="block text-sm font-medium">Allow search engines to show this in results?</label>
          <select v-model="form.seo.seo_index as any" class="mt-1 w-full rounded border px-3 py-2 max-w-xs">
            <option :value="1">Yes</option>
            <option :value="0">No</option>
          </select>
        </div>
        <div class="border-b flex gap-4 text-sm">
          <button type="button" :class="['pb-2', seoActiveTab==='general' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='general'">General Options</button>
          <button type="button" :class="['pb-2', seoActiveTab==='facebook' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='facebook'">Share Facebook</button>
          <button type="button" :class="['pb-2', seoActiveTab==='twitter' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='twitter'">Share Twitter</button>
        </div>
      </div>
      <div class="p-4 space-y-4 max-h-[70vh] overflow-auto">
        <div v-show="seoActiveTab==='general'" class="space-y-4">
          <div>
            <label class="block text-sm">SEO Title</label>
            <input v-model="form.seo.seo_title" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm">SEO Description</label>
            <textarea v-model="form.seo.seo_description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
          </div>
          <div>
            <label class="block text-sm">SEO Keyword</label>
            <textarea v-model="form.seo.seo_keyword" class="mt-1 w-full rounded border px-3 py-2" rows="2" placeholder="Comma separated"></textarea>
          </div>
          <div>
            <label class="block text-sm">SEO Image</label>
            <ImagePicker v-model="form.seo.seo_image" :placeholder="seoPlaceholder" />
            <div v-if="form.errors['seo.seo_image']" class="text-red-600 text-sm mt-1">{{ form.errors['seo.seo_image'] }}</div>
          </div>
        </div>

        <div v-show="seoActiveTab==='facebook'" class="space-y-4">
          <div>
            <label class="block text-sm">Facebook Title</label>
            <input v-model="fb.title" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm">Facebook Description</label>
            <textarea v-model="fb.description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
          </div>
          <div>
            <label class="block text-sm">Facebook Image</label>
            <ImagePicker v-model="fb.image" />
          </div>
        </div>

        <div v-show="seoActiveTab==='twitter'" class="space-y-4">
          <div>
            <label class="block text-sm">Twitter Title</label>
            <input v-model="tw.title" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm">Twitter Description</label>
            <textarea v-model="tw.description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
          </div>
          <div>
            <label class="block text-sm">Twitter Image</label>
            <ImagePicker v-model="tw.image" />
          </div>
        </div>
      </div>
      <div class="px-4 py-3 border-t flex justify-end gap-2">
        <button type="button" class="px-4 py-2 rounded border" @click="seoModalOpen = false">Cancel</button>
        <button type="button" class="px-4 py-2 rounded bg-blue-600 text-white" @click="seoModalOpen = false">Apply</button>
      </div>
    </div>
  </div>
</template>
