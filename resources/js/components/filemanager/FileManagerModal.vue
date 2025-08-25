<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50" @click="close"></div>
    <div class="relative z-10 w-full max-w-5xl bg-white rounded shadow-lg">
      <div class="flex items-center justify-between px-4 py-3 border-b">
        <h3 class="text-lg font-semibold">File Manager</h3>
        <button class="p-2" @click="close">✕</button>
      </div>

      <div class="p-4 space-y-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-2">
            <button class="px-3 py-2 text-sm bg-gray-100 rounded border border-gray-200 hover:bg-gray-200 disabled:opacity-50" @click="goParent" :disabled="!canGoParent">Parent</button>
            <button class="px-3 py-2 text-sm bg-gray-100 rounded border border-gray-200 hover:bg-gray-200 disabled:opacity-50" @click="refresh">Refresh</button>
            <button class="px-3 py-2 text-sm rounded border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50" @click="triggerUpload">Upload</button>
            <button class="px-3 py-2 text-sm bg-gray-100 rounded border border-gray-200 hover:bg-gray-200 disabled:opacity-50" @click="toggleFolder">Folder</button>
            <button class="px-3 py-2 text-sm rounded border border-transparent bg-red-600 text-white hover:bg-red-700 disabled:opacity-50" @click="doDelete" :disabled="selected.length===0">Delete</button>
            <input type="hidden" :value="directory" />
          </div>
          <div class="flex items-stretch gap-2">
            <input v-model="filter" type="text" placeholder="Search" class="px-3 py-2 border border-gray-300 rounded w-64" @keydown.enter="search" />
            <button class="px-3 py-2 text-sm rounded border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50" @click="search">Search</button>
          </div>
        </div>

        <div v-if="showFolder" class="flex items-stretch gap-2">
          <input v-model="newFolder" type="text" placeholder="Folder name" class="px-3 py-2 border border-gray-300 rounded" />
          <button class="px-3 py-2 text-sm rounded border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50" @click="createFolder">Create</button>
        </div>

        <hr />

        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="(d, i) in directories" :key="'d-'+i" class="space-y-2">
            <div class="flex items-center justify-center h-36">
              <a href="#" @click.prevent="openDir(d)" class="text-6xl">📁</a>
            </div>
            <label class="flex items-center gap-2">
              <input type="checkbox" class="w-4 h-4" :value="d.path + '/'" v-model="selected" />
              <span class="truncate" :title="d.name">{{ d.name }}</span>
            </label>
          </div>

          <div v-for="(img, i) in images" :key="'f-'+i" class="space-y-2">
            <div class="flex items-center justify-center h-36">
              <a :href="img.href" @click.prevent="pick(img)" class="block">
                <img :src="img.thumb || img.href" :alt="img.name" class="max-h-36 object-contain" />
              </a>
            </div>
            <label class="flex items-center gap-2">
              <input type="checkbox" class="w-4 h-4" :value="img.path" v-model="selected" />
              <span class="truncate" :title="img.name">{{ img.name }}</span>
            </label>
          </div>
        </div>

        <div v-if="totalPages > 1" class="flex items-center justify-end gap-2 pt-2 border-t">
          <button class="px-3 py-2 text-sm bg-gray-100 rounded border border-gray-200 hover:bg-gray-200 disabled:opacity-50" :disabled="page===1" @click="goto(page-1)">Prev</button>
          <span>Page {{ page }} / {{ totalPages }}</span>
          <button class="px-3 py-2 text-sm bg-gray-100 rounded border border-gray-200 hover:bg-gray-200 disabled:opacity-50" :disabled="page===totalPages" @click="goto(page+1)">Next</button>
        </div>
      </div>

      <input ref="uploadInput" type="file" multiple class="hidden" @change="handleUploadChange" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { fmApi, type FmDirectory, type FmImage } from '@/lib/filemanager'

const props = defineProps<{
  modelValue: boolean
  startDirectory?: string
  maxUploadKB?: number
}>()
const emit = defineEmits<{
  (e: 'update:modelValue', v: boolean): void
  (e: 'picked', payload: { path: string; href: string; name: string; thumb?: string | null }): void
}>()

const open = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})

const directory = ref(props.startDirectory || '')
const filter = ref('')
const page = ref(1)
const limit = 16

const directories = ref<FmDirectory[]>([])
const images = ref<FmImage[]>([])
const pagination = reactive({ total: 0, page: 1, limit })

const selected = ref<string[]>([])
const showFolder = ref(false)
const newFolder = ref('')

const totalPages = computed(() => Math.max(1, Math.ceil(pagination.total / pagination.limit)))
const canGoParent = computed(() => directory.value.includes('/'))

async function load() {
  const res = await fmApi.list({ directory: directory.value, filter_name: filter.value, page: page.value, limit })
  directories.value = res.directories
  images.value = res.images
  pagination.total = res.pagination.total
  pagination.page = res.pagination.page
  selected.value = []
}

function refresh() { load() }
function search() { page.value = 1; load() }
function goto(p: number) { page.value = p; load() }
function openDir(d: FmDirectory) { directory.value = joinPath(directory.value, d.name); page.value = 1; load() }
function goParent() {
  if (!directory.value) return
  const pos = directory.value.lastIndexOf('/')
  directory.value = pos > -1 ? directory.value.slice(0, pos) : ''
  page.value = 1
  load()
}
function toggleFolder() { showFolder.value = !showFolder.value }

async function createFolder() {
  if (!newFolder.value) return
  const res = await fmApi.createFolder(newFolder.value, directory.value)
  if ((res as any).error) alert((res as any).error)
  else { newFolder.value = ''; showFolder.value = false; refresh() }
}

const uploadInput = ref<HTMLInputElement | null>(null)
function triggerUpload() { uploadInput.value?.click() }
async function handleUploadChange(e: Event) {
  const input = e.target as HTMLInputElement
  if (!input.files || input.files.length === 0) return
  // size check similar to OC
  const maxKb = props.maxUploadKB || 1024 * 4
  for (const f of Array.from(input.files)) {
    if (f.size / 1024 > maxKb) { alert('File exceeds max size'); input.value = ''; return }
  }
  const res = await fmApi.upload(Array.from(input.files), directory.value)
  if ((res as any).error) alert((res as any).error)
  else { alert('Uploaded'); refresh() }
  input.value = ''
}

async function doDelete() {
  if (selected.value.length === 0) return
  if (!confirm('Are you sure?')) return
  const res = await fmApi.delete(selected.value)
  if ((res as any).error) alert((res as any).error)
  else { alert('Deleted'); refresh() }
}

function pick(img: FmImage) {
  emit('picked', { path: img.path, href: img.href, name: img.name, thumb: img.thumb })
  close()
}

function close() { open.value = false }

function joinPath(base: string, name: string) {
  return [base, name].filter(Boolean).join('/')
}

watch(() => props.modelValue, (v) => { if (v) load() })

onMounted(() => { if (open.value) load() })
</script>

<style scoped>
</style>
