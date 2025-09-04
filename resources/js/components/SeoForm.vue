<script setup lang="ts">
import { ref, computed } from 'vue'
import ImagePicker from '@/components/filemanager/ImagePicker.vue'

const props = defineProps<{
  modelValue: any // The seo form object
  title: string
  slug: string
  host: string
  showSchema?: boolean
  placeholder?: string
}>()

const emit = defineEmits(['update:modelValue'])

const form = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

// SEO Modal state
const seoModalOpen = ref(false)
const seoActiveTab = ref<'general' | 'facebook' | 'twitter' | 'schema'>('general')

// Ensure defaults for meta_json buckets
if (!form.value.meta_json) form.value.meta_json = {}
if (!form.value.meta_json.facebook) form.value.meta_json.facebook = {}
if (!form.value.meta_json.twitter) form.value.meta_json.twitter = {}
if (props.showSchema && !form.value.meta_json.schema) {
  form.value.meta_json.schema = { enabled: false }
}

function ensureSchemaDefaults() {
  if (!props.showSchema) return
  const s: any = form.value.meta_json.schema
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

const seoPlaceholder = computed(() => props.placeholder ?? '/storage/placeholder.png')

// Helpers to bind facebook/twitter meta_json payloads
const fb = computed({
  get: () => form.value.meta_json.facebook,
  set: (v: any) => (form.value.meta_json = { ...form.value.meta_json, facebook: v }),
})
const tw = computed({
  get: () => form.value.meta_json.twitter,
  set: (v: any) => (form.value.meta_json = { ...form.value.meta_json, twitter: v }),
})

// Schema bindings
const schema = computed({
  get: () => form.value.meta_json.schema,
  set: (v: any) => (form.value.meta_json = { ...form.value.meta_json, schema: v }),
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

const fullUrl = computed(() => {
    try {
        return props.host ? `${props.host}/${props.slug}` : ''
    } catch {
        return ''
    }
})

</script>

<template>
  <div class="rounded border bg-white p-4 space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="font-medium">SEO</h3>
      <button type="button" class="text-blue-600 hover:underline" @click="seoModalOpen = true">Edit</button>
    </div>
    <div class="border rounded p-3">
      <div class="text-xs text-gray-500">Search engine</div>
      <div class="mt-2">
        <div class="text-sm text-gray-600 truncate">{{ fullUrl }}</div>
        <div class="text-xl text-blue-700 leading-tight">{{ form.seo_title || title }}</div>
        <div class="text-gray-700">{{ form.seo_description }}</div>
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
          <select v-model="form.seo_index" class="mt-1 w-full rounded border px-3 py-2 max-w-xs">
            <option :value="1">Yes</option>
            <option :value="0">No</option>
          </select>
        </div>
        <div class="border-b flex gap-4 text-sm">
          <button type="button" :class="['pb-2', seoActiveTab==='general' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='general'">General Options</button>
          <button class="hidden" type="button" :class="['pb-2', seoActiveTab==='facebook' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='facebook'">Share Facebook</button>
          <button class="hidden" type="button" :class="['pb-2', seoActiveTab==='twitter' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='twitter'">Share Twitter</button>
          <button v-if="showSchema" type="button" :class="['pb-2', seoActiveTab==='schema' ? 'border-b-2 border-blue-600' : 'text-gray-500']" @click="seoActiveTab='schema'">Schema</button>
        </div>
      </div>
      <div class="p-4 space-y-4 max-h-[70vh] overflow-auto">
        <div v-show="seoActiveTab==='general'" class="space-y-4">
          <div>
            <label class="block text-sm">SEO Title</label>
            <input v-model="form.seo_title" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm">SEO Description</label>
            <textarea v-model="form.seo_description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
          </div>
          <div>
            <label class="block text-sm">SEO Keyword</label>
            <textarea v-model="form.seo_keyword" class="mt-1 w-full rounded border px-3 py-2" rows="2" placeholder="Comma separated"></textarea>
          </div>
          <div>
            <label class="block text-sm">SEO Image</label>
            <ImagePicker v-model="form.seo_image" :placeholder="seoPlaceholder" />
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

        <div v-if="showSchema" v-show="seoActiveTab==='schema'" class="space-y-6">
          <div class="flex items-center gap-2">
            <input id="schema-enabled" type="checkbox" v-model="schema.enabled" />
            <label for="schema-enabled" class="text-sm">Enable structured data (JSON-LD)</label>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm">Schema Type (@type)</label>
              <input v-model="schema['@type']" type="text" class="mt-1 w-full rounded border px-3 py-2" placeholder="WebPage" />
            </div>
            <div>
              <label class="block text-sm">Name</label>
              <input v-model="schema.name" type="text" class="mt-1 w-full rounded border px-3 py-2" />
            </div>
          </div>

          <div>
            <label class="block text-sm">Description</label>
            <textarea v-model="schema.description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
          </div>

          <div class="border rounded p-3 space-y-4">
            <div class="font-medium text-sm">Main Entity</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm">@type</label>
                <input v-model="schema.mainEntity['@type']" type="text" class="mt-1 w-full rounded border px-3 py-2" placeholder="SoftwareApplication" />
              </div>
              <div>
                <label class="block text-sm">Name</label>
                <input v-model="schema.mainEntity.name" type="text" class="mt-1 w-full rounded border px-3 py-2" />
              </div>
              <div>
                <label class="block text-sm">Operating System</label>
                <input v-model="schema.mainEntity.operatingSystem" type="text" class="mt-1 w-full rounded border px-3 py-2" placeholder="Cloud-based" />
              </div>
              <div>
                <label class="block text-sm">Application Category</label>
                <input v-model="schema.mainEntity.applicationCategory" type="text" class="mt-1 w-full rounded border px-3 py-2" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm">Price</label>
                <input v-model="schema.mainEntity.offers.price" type="text" class="mt-1 w-full rounded border px-3 py-2" />
              </div>
              <div>
                <label class="block text-sm">Price Currency</label>
                <input v-model="schema.mainEntity.offers.priceCurrency" type="text" class="mt-1 w-full rounded border px-3 py-2" placeholder="USD" />
              </div>
            </div>

            <div>
              <label class="block text-sm">Feature List (one per line)</label>
              <textarea v-model="schemaFeatures" class="mt-1 w-full rounded border px-3 py-2" rows="4" placeholder="e.g. Real-time verification\nAI-powered risk scoring"></textarea>
            </div>

            <div>
              <label class="block text-sm">Main Entity Description</label>
              <textarea v-model="schema.mainEntity.description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
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
