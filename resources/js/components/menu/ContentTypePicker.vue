<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'

type ContentType = { class: string; name: string; searchable: boolean }
type ContentItem = { id: number|string; name: string; url: string; item_model: string }

const emit = defineEmits<{ (e: 'add-items', items: any[]): void }>()

const types = ref<ContentType[]>([])
const activeClass = ref<string>('')
const search = ref('')
const results = ref<ContentItem[]>([])
const loading = ref(false)
const errorMsg = ref('')
const openPanels = ref<Record<string, boolean>>({})
const selected = ref<Record<string, Set<string>>>({})
// Custom URL state
const customLabel = ref('')
const customUrl = ref('')

async function fetchTypes() {
  const res = await fetch(route('admin.menu.content_types'), { credentials: 'same-origin' })
  types.value = await res.json()
  activeClass.value = types.value[0]?.class || ''
  // initialize accordion state
  types.value.forEach(t => {
    openPanels.value[t.class] = t.class === activeClass.value
    selected.value[t.class] = new Set<string>()
  })
}

async function doSearch() {
  if (!activeClass.value) return
  loading.value = true
  errorMsg.value = ''
  try {
    const token = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content
    const res = await fetch(route('admin.menu.search_content'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token || '' },
      credentials: 'same-origin',
      body: JSON.stringify({ class: activeClass.value, q: search.value })
    })
    if (!res.ok) {
      errorMsg.value = `Search failed (${res.status})`
      results.value = []
      return
    }
    results.value = await res.json()
  } finally {
    loading.value = false
  }
}

// bulkAdd and addCustomUrl emit to parent

function toggleSelect(cls: string, item: ContentItem, checked: boolean) {
  const key = `${item.item_model}:${item.id}`
  const set = selected.value[cls] ?? new Set<string>()
  if (checked) set.add(key)
  else set.delete(key)
  selected.value[cls] = set
}

function bulkAdd(cls: string) {
  const keys = Array.from(selected.value[cls] || [])
  const toAdd = results.value.filter(r => keys.includes(`${r.item_model}:${r.id}`))
  if (toAdd.length) emit('add-items', toAdd)
}

function addCustomUrl() {
  if (!customLabel.value || !customUrl.value) return
  emit('add-items', [{ id: `custom:${Date.now()}`, name: customLabel.value, url: customUrl.value, item_model: 'custom' }])
  customLabel.value = ''
  customUrl.value = ''
}

onMounted(() => {
  fetchTypes()
})

// Auto search when active type changes
watch(activeClass, () => {
  doSearch()
})

// Debounce search input
let searchTimer: any
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => doSearch(), 300)
})
</script>

<template>
  <div class="space-y-3">
    <!-- Accordion per type -->
    <div v-for="t in types" :key="t.class" class="border rounded bg-white">
      <button type="button" class="w-full flex items-center justify-between px-3 py-2 border-b" @click="openPanels[t.class] = !openPanels[t.class]; activeClass = t.class">
        <span class="font-medium">{{ t.name }}</span>
        <span class="text-gray-500"><i class="fa fa-chevron-down" :class="{ 'rotate-180': openPanels[t.class] }"></i></span>
      </button>
      <div v-show="openPanels[t.class]" class="p-3 space-y-2">
        <div class="flex items-center gap-2">
          <input v-model="search" type="text" placeholder="Search..." class="w-full border rounded px-3 py-2" />
          <button type="button" class="px-3 py-2 rounded bg-gray-900 text-white" @click="doSearch" :disabled="loading">Search</button>
        </div>
        <div class="max-h-56 overflow-auto border rounded">
          <div v-if="loading" class="p-3 text-sm text-gray-500">Loading...</div>
          <div v-else-if="errorMsg" class="p-3 text-sm text-red-600">{{ errorMsg }}</div>
          <ul v-else-if="results.length" class="divide-y">
            <li v-for="r in results" :key="`${r.item_model}:${r.id}`" class="p-2 flex items-center gap-2">
              <input type="checkbox" :value="`${r.item_model}:${r.id}`" :checked="selected[t.class]?.has(`${r.item_model}:${r.id}`)" @change="(e:any) => toggleSelect(t.class, r, e.target.checked)" />
              <div class="flex-1">
                <div class="font-medium text-sm">{{ r.name }}</div>
                <div class="text-xs text-gray-500">{{ r.url }}</div>
              </div>
            </li>
          </ul>
          <div v-else class="p-3 text-sm text-gray-500">No items found</div>
        </div>
        <div class="text-right">
          <button type="button" class="mt-2 px-3 py-2 rounded bg-blue-600 text-white" @click="bulkAdd(t.class)">Add to Menu</button>
        </div>
      </div>
    </div>

    <!-- Custom URL panel -->
    <div class="border rounded bg-white">
      <button type="button" class="w-full flex items-center justify-between px-3 py-2 border-b" @click="openPanels.custom = !openPanels.custom">
        <span class="font-medium">Custom Url</span>
        <span class="text-gray-500"><i class="fa fa-chevron-down" :class="{ 'rotate-180': openPanels.custom }"></i></span>
      </button>
      <div v-show="openPanels.custom" class="p-3 space-y-2">
        <input v-model="customLabel" type="text" placeholder="Label" class="w-full border rounded px-3 py-2" />
        <input v-model="customUrl" type="text" placeholder="https://example.com" class="w-full border rounded px-3 py-2" />
        <div class="text-right">
          <button type="button" class="mt-2 px-3 py-2 rounded bg-blue-600 text-white" @click="addCustomUrl">Add to Menu</button>
        </div>
      </div>
    </div>
  </div>
</template>
