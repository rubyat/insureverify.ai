<script lang="ts" setup>
import { ref, watch, onMounted, computed } from 'vue'
import axios from 'axios'

/** Server response shapes (parity with your OC controller) */
type DirectoryItem = { name: string; path: string }                      // e.g. "banners/"
type ImageItem     = { name: string; path: string; href: string; thumb: string }
type Pagination    = { total: number; per_page: number; current_page: number; last_page: number }
type ListResponse  = {
  directories: DirectoryItem[]
  images: ImageItem[]
  directory: string
  filter_name: string
  pagination: Pagination
  parent: string
  config_file_max_size: number // BYTES
}

const props = defineProps<{
  /** Two-way bound selected value, e.g. "catalog/banners/hero.jpg" */
  modelValue?: string
  /** Controls modal visibility */
  open?: boolean
  /** API base (matches routes I shared): /admin/api/filemanager */
  apiBase?: string
  /** Thumb endpoint base (matches ThumbController): /thumb */
  thumbBase?: string
  /** Thumb size used in grid + preview */
  thumbW?: number
  thumbH?: number
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', v: string): void
  (e: 'update:open', v: boolean): void
}>()

// defaults
const apiBase   = computed(() => props.apiBase  ?? '/admin/api/filemanager')
const thumbW    = computed(() => props.thumbW ?? 300)
const thumbH    = computed(() => props.thumbH ?? 300)

// state (parity with Twig inputs)
const directory = ref<string>('')                    // hidden #input-directory in Twig
const filter    = ref<string>('')                    // #input-search
const page      = ref<number>(1)

const busy      = ref<boolean>(false)
const showCreateFolder = ref<boolean>(false)
const newFolderName = ref<string>('')

// checkbox selection for delete (dirs + files)
const selection = ref<Set<string>>(new Set())

const data = ref<ListResponse>({
  directories: [],
  images: [],
  directory: '',
  filter_name: '',
  pagination: { total: 0, per_page: 16, current_page: 1, last_page: 1 },
  parent: '',
  config_file_max_size: 10 * 1024 * 1024
})

function clearSelection() { selection.value.clear() }

async function load() {
  busy.value = true
  try {
    const res = await axios.get<ListResponse>(`${apiBase.value}/list`, {
      params: {
        directory: directory.value || undefined,
        filter_name: filter.value || undefined,
        page: page.value,
        thumb_w: thumbW.value,
        thumb_h: thumbH.value
      }
    })
    data.value = res.data
  } finally {
    busy.value = false
  }
}

function openDir(path: string) {
  directory.value = (path || '').replace(/^\/|\/$/g, '')
  page.value = 1
  clearSelection()
  load()
}

function goParent() {
  openDir(data.value.parent || '')
}

function refresh() {
  load()
}

function onSearch() {
  page.value = 1
  load()
}

function onClose() {
  emit('update:open', false)
}

// choose → fill hidden input & close (mirrors Twig "a.thumbnail" click)
function pick(img: ImageItem) {
  const rel = img.path.startsWith('catalog/') ? img.path : `catalog/${img.path}`
  emit('update:modelValue', rel)
  emit('update:open', false)
}

// Upload (mirrors Twig: hidden form + multiple files)
async function browseUpload() {
  const input = document.createElement('input')
  input.type = 'file'
  input.multiple = true
  input.accept = '.ico,.gif,.jpg,.jpe,.jpeg,.png,.webp'

  input.onchange = async () => {
    const files = Array.from(input.files ?? [])
    if (!files.length) return

    // client-side size check (Twig compares against config)
    const maxBytes = data.value.config_file_max_size
    for (const f of files) {
      if (f.size > maxBytes) {
        alert(`"${f.name}" exceeds limit (${(maxBytes/1024/1024).toFixed(1)} MB).`)
        return
      }
    }

    const form = new FormData()
    files.forEach(f => form.append('file[]', f))

    const url = new URL(`${apiBase.value}/upload`, window.location.origin)
    if (directory.value) url.searchParams.set('directory', directory.value)

    try {
      await axios.post(url.toString(), form, { headers: { 'Content-Type': 'multipart/form-data' } })
      await load()
      alert('Uploaded!')
    } catch (e: any) {
      alert(e?.response?.data?.error ?? 'Upload failed')
    }
  }

  input.click()
}

// Create folder (mirrors #modal-folder / #button-create)
async function createFolder(name: string) {
  if (!name) return
  const url = new URL(`${apiBase.value}/folder`, window.location.origin)
  if (directory.value) url.searchParams.set('directory', directory.value)
  try {
    await axios.post(url.toString(), { folder: name })
    showCreateFolder.value = false
    await load()
    alert('Folder created')
  } catch (e: any) {
    alert(e?.response?.data?.error ?? 'Failed to create folder')
  }
}

// Delete (mirrors Twig #button-delete posting checked "path[]")
async function delSelected() {
  if (!selection.value.size) return
  if (!confirm('Delete selected?')) return
  try {
    await axios.post(`${apiBase.value}/delete`, { path: Array.from(selection.value) })
    selection.value.clear()
    await load()
    alert('Deleted')
  } catch (e: any) {
    alert(e?.response?.data?.error ?? 'Delete failed')
  }
}

// checkbox helpers
function toggleSelect(path: string) {
  if (selection.value.has(path)) selection.value.delete(path)
  else selection.value.add(path)
}

watch(() => props.open, (v) => { if (v) load() })
onMounted(() => { if (props.open) load() })
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50">
    <!-- backdrop -->
    <div class="absolute inset-0 bg-black/40" @click="onClose" />
    <!-- modal -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- header -->
        <div class="flex items-center justify-between px-4 py-3 border-b">
          <h5 class="text-lg font-semibold">Image Manager</h5>
          <button class="px-3 py-1.5 rounded-md border" @click="onClose">Close</button>
        </div>

        <!-- toolbar (parity with Twig top row) -->
        <div class="px-4 py-3 flex flex-wrap items-center gap-2">
          <button id="button-parent" class="px-3 py-1.5 rounded-md border" title="Parent" @click="goParent">
            <!-- up icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 10l8-8 8 8M12 2v20"/></svg>
          </button>

          <button id="button-refresh" class="px-3 py-1.5 rounded-md border" title="Refresh" @click="refresh">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 12a9 9 0 1 1-6.219-8.56"/><path d="M21 3v7h-7"/></svg>
          </button>

          <button id="button-upload" class="px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700" title="Upload" @click="browseUpload">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M20 16v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2"/></svg> Upload
          </button>

          <button id="button-folder" class="px-3 py-1.5 rounded-md border" title="New Folder" @click="showCreateFolder = !showCreateFolder">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h5l2 2h11v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M12 10v6M9 13h6"/></svg>
          </button>

          <button id="button-delete" class="px-3 py-1.5 rounded-md bg-red-600 text-white hover:bg-red-700" title="Delete" @click="delSelected">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6l1-2h6l1 2M6 6l1 14h10l1-14"/></svg> Delete
          </button>

          <!-- hidden "directory" (Twig keeps it; we mirror for parity & devtools) -->
          <input type="hidden" name="directory" :value="directory" id="input-directory">

          <!-- search (parity with right side of Twig row) -->
          <div class="ml-auto flex items-center gap-2">
            <input id="input-search" class="px-3 py-1.5 rounded-md border text-sm" v-model="filter" placeholder="Search (prefix)" @keydown.enter="onSearch">
            <button id="button-search" class="px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700" title="Search" @click="onSearch">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </button>
          </div>
        </div>

        <!-- create folder panel (parity with #modal-folder) -->
        <div v-show="showCreateFolder" class="px-4 pb-3">
          <div class="flex gap-2">
            <input id="input-folder" class="px-3 py-1.5 rounded-md border text-sm flex-1" placeholder="Folder name"
                   v-model="newFolderName"
                   @keyup.enter="() => { createFolder(newFolderName); newFolderName=''; }">
            <button id="button-create" class="px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700"
                    @click="() => { if (newFolderName) { createFolder(newFolderName); newFolderName=''; } }">
              Create
            </button>
          </div>
        </div>

        <hr class="border-gray-200">

        <!-- body grid (parity with Twig list) -->
        <div class="px-4 pb-4">
          <div v-if="busy" class="p-6 text-center text-gray-500">Loading…</div>

          <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- directories -->
            <div v-for="(d) in data.directories" :key="'d-'+d.path" class="mb-3">
              <div class="mb-1 min-h-[140px] flex items-center justify-center">
                <a href="#" class="mb-1" @click.prevent="openDir(d.path)" title="Open folder">
                  <!-- folder icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-yellow-500" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 4l2 2h8a2 2 0 0 1 2 2v9a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h5z"/>
                  </svg>
                </a>
              </div>
              <label class="flex items-center gap-2 text-sm">
                <input class="rounded border-gray-300" type="checkbox"
                       :value="d.path" :checked="selection.has(d.path)"
                       @change.stop="toggleSelect(d.path)">
                <span class="truncate">{{ d.name }}</span>
              </label>
            </div>

            <!-- images -->
            <div v-for="(img) in data.images" :key="'f-'+img.path" class="mb-3 border rounded-xl overflow-hidden">
              <div class="mb-1 min-h-[140px]">
                <a :href="img.href" class="mb-1 block" @click.prevent="pick(img)">
                  <img :src="img.thumb" :alt="img.name" :title="img.name"
                       class="w-full aspect-square object-contain bg-gray-50" />
                </a>
              </div>
              <div class="p-2">
                <label class="flex items-center gap-2 text-sm">
                  <input class="rounded border-gray-300" type="checkbox"
                         :value="img.path" :checked="selection.has(img.path)"
                         @change.stop="toggleSelect(img.path)">
                  <span class="truncate" :title="img.name">{{ img.name }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- pagination (parity with Twig footer) -->
          <div v-if="data.pagination && data.pagination.last_page > 1"
               class="mt-4 flex items-center justify-center gap-2">
            <button class="px-3 py-1.5 rounded-md border"
                    :disabled="page<=1"
                    @click="page--; load()">
              Prev
            </button>
            <div class="text-sm">
              Page {{ data.pagination.current_page }} / {{ data.pagination.last_page }}
            </div>
            <button class="px-3 py-1.5 rounded-md border"
                    :disabled="page>=data.pagination.last_page"
                    @click="page++; load()">
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
