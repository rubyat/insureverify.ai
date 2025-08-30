<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import ImagePicker from '@/components/filemanager/ImagePicker.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

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

// SEO Modal state
const seoModalOpen = ref(false)
const seoActiveTab = ref<'general' | 'facebook' | 'twitter' | 'schema'>('general')

// Ensure defaults for meta_json buckets
if (!form.seo.meta_json) form.seo.meta_json = {}
if (!form.seo.meta_json.facebook) form.seo.meta_json.facebook = {}
if (!form.seo.meta_json.twitter) form.seo.meta_json.twitter = {}
if (!form.seo.meta_json.schema) form.seo.meta_json.schema = { enabled: false }

function ensureSchemaDefaults() {
  const s: any = form.seo.meta_json.schema
  if (!s['@type']) s['@type'] = 'WebPage'
  if (!('name' in s)) s.name = ''
  if (!('description' in s)) s.description = ''
  if (!s.mainEntity) s.mainEntity = {}
  if (!s.mainEntity['@type']) s.mainEntity['@type'] = 'SoftwareApplication'
  if (!('name' in s.mainEntity)) s.mainEntity.name = ''
  if (!('operatingSystem' in s.mainEntity)) s.mainEntity.operatingSystem = 'Cloud-based'
  if (!('applicationCategory' in s.mainEntity)) s.mainEntity.applicationCategory = ''
  if (!s.mainEntity.offers) s.mainEntity.offers = {}
  if (!('price' in s.mainEntity.offers)) s.mainEntity.offers.price = ''
  if (!('priceCurrency' in s.mainEntity.offers)) s.mainEntity.offers.priceCurrency = ''
  if (!Array.isArray(s.mainEntity.featureList)) s.mainEntity.featureList = []
}
ensureSchemaDefaults()

// Placeholder for SEO image comes from backend (Seo::$appends['placeholder'])
const seoPlaceholder = computed(() => (props.page?.seo?.placeholder as string | undefined) ?? '/storage/placeholder.png')

// Helpers to bind facebook/twitter meta_json payloads without side effects in getters
const fb = computed({
  get: () => form.seo.meta_json.facebook,
  set: (v: any) => (form.seo.meta_json = { ...form.seo.meta_json, facebook: v }),
})
const tw = computed({
  get: () => form.seo.meta_json.twitter,
  set: (v: any) => (form.seo.meta_json = { ...form.seo.meta_json, twitter: v }),
})

// Schema bindings (used by Schema tab form)
const schema = computed({
  get: () => form.seo.meta_json.schema,
  set: (v: any) => (form.seo.meta_json = { ...form.seo.meta_json, schema: v }),
})

// Feature list helper for textarea editing
const schemaFeatures = computed({
  get: () => (schema.value?.mainEntity?.featureList ?? []).join('\n'),
  set: (v: string) => {
    const lines = (v || '')
      .split(/\r?\n/)
      .map((s) => s.trim())
      .filter(Boolean)
    const me = { ...(schema.value?.mainEntity ?? {}), featureList: lines }
    schema.value = { ...(schema.value ?? {}), mainEntity: me }
  },
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
        <div class="hidden">
          <label class="block text-sm font-medium">Content (fallback)</label>
          <QuillEditor v-model:content="quillContent" contentType="html" theme="snow" class="mt-1 bg-white" />
        </div>
      </div>

      <div class="rounded border bg-white p-4 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-medium">SEO</h3>
          <button type="button" class="text-blue-600 hover:underline" @click="seoModalOpen = true">Edit</button>
        </div>
        <!-- Preview like screenshot -->
        <div class="border rounded p-3">
          <div class="text-xs text-gray-500">Search engine</div>
          <div class="mt-2">
            <div class="text-sm text-gray-600 truncate">{{ (props.page ? route('home') + '/' + form.slug : '') }}</div>
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
          <button v-if="!props.page" :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitCreate">Create</button>
          <button v-else :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitUpdate(props.page.id)">Save</button>
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
          <button v-if="false" type="button" :class="['pb-2', seoActiveTab==='facebook' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='facebook'">Share Facebook</button>
          <button v-if="false" type="button" :class="['pb-2', seoActiveTab==='twitter' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='twitter'">Share Twitter</button>
          <button type="button" :class="['pb-2', seoActiveTab==='schema' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='schema'">Schema</button>
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

        <!-- Schema Form -->
        <div v-show="seoActiveTab==='schema'" class="space-y-6">
          <div class="flex items-center gap-2">
            <input id="schema-enabled" type="checkbox" v-model="form.seo.meta_json.schema.enabled" />
            <label for="schema-enabled" class="text-sm">Enable structured data (JSON-LD)</label>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm">Schema Type (@type)</label>
              <input v-model="form.seo.meta_json.schema['@type']" type="text" class="mt-1 w-full rounded border px-3 py-2" placeholder="WebPage" />
            </div>
            <div>
              <label class="block text-sm">Name</label>
              <input v-model="form.seo.meta_json.schema.name" type="text" class="mt-1 w-full rounded border px-3 py-2" />
            </div>
          </div>

          <div>
            <label class="block text-sm">Description</label>
            <textarea v-model="form.seo.meta_json.schema.description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
          </div>

          <div class="border rounded p-3 space-y-4">
            <div class="font-medium text-sm">Main Entity</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm">@type</label>
                <input v-model="form.seo.meta_json.schema.mainEntity['@type']" type="text" class="mt-1 w-full rounded border px-3 py-2" placeholder="SoftwareApplication" />
              </div>
              <div>
                <label class="block text-sm">Name</label>
                <input v-model="form.seo.meta_json.schema.mainEntity.name" type="text" class="mt-1 w-full rounded border px-3 py-2" />
              </div>
              <div>
                <label class="block text-sm">Operating System</label>
                <input v-model="form.seo.meta_json.schema.mainEntity.operatingSystem" type="text" class="mt-1 w-full rounded border px-3 py-2" placeholder="Cloud-based" />
              </div>
              <div>
                <label class="block text-sm">Application Category</label>
                <input v-model="form.seo.meta_json.schema.mainEntity.applicationCategory" type="text" class="mt-1 w-full rounded border px-3 py-2" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm">Price</label>
                <input v-model="form.seo.meta_json.schema.mainEntity.offers.price" type="text" class="mt-1 w-full rounded border px-3 py-2" />
              </div>
              <div>
                <label class="block text-sm">Price Currency</label>
                <input v-model="form.seo.meta_json.schema.mainEntity.offers.priceCurrency" type="text" class="mt-1 w-full rounded border px-3 py-2" placeholder="USD" />
              </div>
            </div>

            <div>
              <label class="block text-sm">Feature List (one per line)</label>
              <textarea v-model="schemaFeatures" class="mt-1 w-full rounded border px-3 py-2" rows="4" placeholder="e.g. Real-time verification\nAI-powered risk scoring"></textarea>
            </div>

            <div>
              <label class="block text-sm">Main Entity Description</label>
              <textarea v-model="form.seo.meta_json.schema.mainEntity.description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
            </div>
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

