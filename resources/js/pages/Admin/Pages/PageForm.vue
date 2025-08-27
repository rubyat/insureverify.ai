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

type PageFormPayload = {
  title: string
  slug: string
  status: number
  short_desc: string
  image_id: string | undefined
  custom_logo: string | undefined
  header_style: string
  show_template: number
  content: string
  seo: {
    seo_title: string
    seo_description: string
    seo_image_id: string | undefined
    canonical_url: string
  }
}

const form = useForm<PageFormPayload>({
  title: props.page?.title ?? '',
  slug: props.page?.slug ?? '',
  status: props.page?.status ?? 1,
  short_desc: props.page?.short_desc ?? '',
  image_id: props.page?.image_id ?? undefined,
  custom_logo: props.page?.custom_logo ?? undefined,
  header_style: props.page?.header_style ?? '',
  show_template: props.page?.show_template ?? 1,
  content: props.page?.content ?? '',
  seo: {
    seo_title: props.page?.seo?.seo_title ?? '',
    seo_description: props.page?.seo?.seo_description ?? '',
    seo_image_id: props.page?.seo?.seo_image_id ?? undefined,
    canonical_url: props.page?.seo?.canonical_url ?? '',
  },
})

// Transform payload to coerce IDs to integers or null
form.transform((data: any) => {
  const toIntOrNull = (v: any) => {
    const n = Number(v)
    return Number.isFinite(n) && String(v).trim() !== '' ? n : null
  }
  return {
    ...data,
    image_id: toIntOrNull(data.image_id),
    custom_logo: toIntOrNull(data.custom_logo),
    seo: {
      ...data.seo,
      seo_image_id: toIntOrNull(data.seo?.seo_image_id),
    },
  }
})

const submitting = ref(false)

// Simpler computed to avoid template expanding complex error mapped types
const hasErrors = computed(() => Object.keys((form.errors as unknown as Record<string, any>) || {}).length > 0)

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

const emit = defineEmits<{ (e: 'create'): void; (e: 'update'): void }>()

const quillContent = ref(form.content)
watch(quillContent, (v) => (form.content = v ?? ''))
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
          <input v-model="form.slug" type="text" class="mt-1 w-full rounded border px-3 py-2" />
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
        </div>
        <div>
          <label class="block text-sm">SEO Title</label>
          <input v-model="form.seo.seo_title" type="text" class="mt-1 w-full rounded border px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm">SEO Description</label>
          <textarea v-model="form.seo.seo_description" class="mt-1 w-full rounded border px-3 py-2" rows="3"></textarea>
        </div>
        <div>
          <label class="block text-sm">Canonical URL</label>
          <input v-model="form.seo.canonical_url" type="text" class="mt-1 w-full rounded border px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm">SEO Image</label>
          <ImagePicker v-model="form.seo.seo_image_id" />
          <div v-if="form.errors['seo.seo_image_id']" class="text-red-600 text-sm mt-1">{{ form.errors['seo.seo_image_id'] }}</div>
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
        <div>
          <label class="block text-sm font-medium">Show Template</label>
          <select v-model.number="form.show_template" class="mt-1 w-full rounded border px-3 py-2">
            <option :value="1">Template</option>
            <option :value="0">Fallback Content</option>
          </select>
        </div>
        <div class="pt-2">
          <button v-if="!props.page" :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitCreate">Create</button>
          <button v-else :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitUpdate(props.page.id)">Save</button>
        </div>
      </div>

      <div class="rounded border bg-white p-4 space-y-3">
        <h3 class="font-medium">Featured Image</h3>
        <ImagePicker v-model="form.image_id" />
        <div v-if="form.errors.image_id" class="text-red-600 text-sm mt-1">{{ form.errors.image_id }}</div>
      </div>
    </div>
  </div>
</template>
