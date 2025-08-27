<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DynamicForm from './components/DynamicForm.vue'
import BlockRenderer from './components/BlockRenderer.vue'

const props = defineProps<{
  page: any
  template: Record<string, any>
  blocksEndpoint: string
  livePreviewEndpoint: string
  saveEndpoint: string
}>()

const catalog = ref<any[]>([])
const tpl = ref<Record<string, any>>({ ...props.template })

// Load blocks catalog
fetch(props.blocksEndpoint)
  .then((r) => r.json())
  .then((json) => (catalog.value = json))

const selectedNodeId = ref<string | null>(null)
const selectedNode = computed(() => (selectedNodeId.value ? tpl.value[selectedNodeId.value] : null))
const showRight = computed(() => !!selectedNodeId.value && selectedNodeId.value !== 'ROOT')

function newId() {
  return 'n' + Math.random().toString(36).slice(2, 9)
}

function ensureRoot() {
  if (!tpl.value.ROOT) {
    tpl.value.ROOT = { type: 'root', nodes: [], version: '1.1' }
  }
}

function addBlock(blockId: string, parentId = 'ROOT') {
  ensureRoot()
  const id = newId()
  const blk = findBlock(blockId)
  if (!blk) return
  const modelDefaults = blk.model ?? {}
  tpl.value[id] = { id, type: blockId, model: { ...modelDefaults }, parent: parentId }
  const parent = tpl.value[parentId]
  parent.nodes ??= []
  parent.nodes.push(id)
  selectedNodeId.value = id
}

function removeSelected() {
  const id = selectedNodeId.value
  if (!id || id === 'ROOT') return
  const parentId = tpl.value[id]?.parent || 'ROOT'
  const parent = tpl.value[parentId]
  if (parent?.nodes) parent.nodes = parent.nodes.filter((n: string) => n !== id)
  delete tpl.value[id]
  selectedNodeId.value = 'ROOT'
}

function findBlock(blockId: string) {
  for (const g of catalog.value) {
    const f = (g.items || []).find((it: any) => it.id === blockId)
    if (f) return f
  }
  return null
}

async function saveTemplate() {
  const res = await fetch(props.saveEndpoint, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '' },
    body: JSON.stringify({ template: tpl.value }),
  })
  if (res.ok) {
    const json = await res.json()
    // Replace local tpl with server-saved template (in case backend normalizes/augments it)
    if (json?.template) {
      tpl.value = { ...json.template }
    }
    lastSaved.value = new Date().toLocaleString()
  }
}

const lastSaved = ref<string>('')
const isSaving = ref<boolean>(false)

function renderTree(nodeId: string) {
  const node = tpl.value[nodeId]
  if (!node) return []
  const ids: string[] = node.nodes || []
  return ids
}

function onUpdateNode(updated: any) {
  if (!updated || !updated.id) return
  tpl.value[updated.id] = { ...(tpl.value[updated.id] || {}), ...updated }
}

// Drag & Drop sorting for ROOT layer
const draggingId = ref<string | null>(null)
function onLayerDragStart(id: string) {
  draggingId.value = id
}
function onLayerDragOver(e: DragEvent) {
  // Allow dropping by preventing default
  e.preventDefault()
}
function onLayerDrop(targetId: string) {
  if (!tpl.value.ROOT || !tpl.value.ROOT.nodes) return
  const nodes: string[] = tpl.value.ROOT.nodes
  const from = draggingId.value ? nodes.indexOf(draggingId.value) : -1
  const to = nodes.indexOf(targetId)
  if (from === -1 || to === -1 || from === to) {
    draggingId.value = null
    return
  }
  const [moved] = nodes.splice(from, 1)
  // Insert before the target index
  nodes.splice(to, 0, moved)
  draggingId.value = null
}
function onLayerDropEnd() {
  if (!tpl.value.ROOT || !tpl.value.ROOT.nodes) return
  const nodes: string[] = tpl.value.ROOT.nodes
  const from = draggingId.value ? nodes.indexOf(draggingId.value) : -1
  if (from === -1) {
    draggingId.value = null
    return
  }
  const [moved] = nodes.splice(from, 1)
  nodes.push(moved)
  draggingId.value = null
}

async function saveAndPreviewAll() {
  try {
    isSaving.value = true
    await saveTemplate()
    // client-side preview updates live with tpl; no need to call server render
  } finally {
    isSaving.value = false
  }
}

async function openLivePreview() {
  try {
    isSaving.value = true
    await saveTemplate()
    window.open(props.livePreviewEndpoint, '_blank')
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <Head :title="`${page.title} • Template Builder`" />
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Pages', href: route('admin.pages.index') },
    { title: `${page.title} • Template Builder`, href: '#' },
  ]">
    <div class="p-0 lg:p-6 bg-gray-50 h-full">
      <div class="flex items-center justify-between px-4 py-3 border-b bg-white">
        <div class="font-medium">{{ page.title }} • Template Builder</div>
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500" v-if="lastSaved">Last saved: {{ lastSaved }}</span>
          <button
            class="rounded bg-primary px-4 py-2 text-white inline-flex items-center gap-2 disabled:opacity-60"
            @click="saveAndPreviewAll"
            :disabled="isSaving"
          >
            <svg v-if="isSaving" class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
            <span>{{ isSaving ? 'Saving…' : 'Save' }}</span>
          </button>
          <button
            class="rounded border px-4 py-2 text-sm inline-flex items-center gap-2 disabled:opacity-60"
            @click="openLivePreview"
            :disabled="isSaving"
          >
            <i class="fa-regular fa-window-restore"></i>
            <span>Live Preview</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 p-4">
        <!-- Left: Layers + Add -->
        <aside class="col-span-3 space-y-4">
          <div class="rounded border bg-white">
            <div class="px-3 py-2 font-medium border-b">Layers</div>
            <div class="p-2 text-sm" @dragover="onLayerDragOver" @drop="onLayerDropEnd">
              <button class="hidden w-full text-left rounded px-2 py-1 hover:bg-gray-100" :class="{ 'bg-blue-50': selectedNodeId==='ROOT' }" @click="selectedNodeId='ROOT'">ROOT</button>
              <template v-for="id in renderTree('ROOT')" :key="id">
                <button
                  class="w-full text-left rounded px-2 py-1 hover:bg-gray-100"
                  :class="{ 'bg-blue-50': selectedNodeId===id }"
                  @click="selectedNodeId=id"
                  draggable="true"
                  @dragstart="onLayerDragStart(id)"
                  @dragover="onLayerDragOver"
                  @drop="onLayerDrop(id)"
                >
                  {{ tpl[id]?.type }}
                </button>
              </template>
            </div>
          </div>

          <div class="rounded border bg-white">
            <div class="px-3 py-2 font-medium border-b">Add Block</div>
            <div class="p-2 space-y-3">
              <div v-for="g in catalog" :key="g.name">
                <div class="px-2 text-xs uppercase text-gray-500">{{ g.name }}</div>
                <div class="p-2 grid grid-cols-2 gap-2">
                  <button v-for="b in g.items" :key="b.id" class="rounded border px-2 py-1 text-sm hover:bg-gray-50" @click="addBlock(b.id)">{{ b.name }}</button>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <!-- Center: Preview (Client-side Vue) -->
        <main :class="showRight ? 'col-span-6' : 'col-span-9'">
          <div class="rounded border bg-white h-[70vh] overflow-auto">
            <div class="px-3 py-2 font-medium border-b flex items-center justify-between">
              <div>Preview</div>
              <div class="space-x-2 hidden"></div>
            </div>
            <div class="p-4">
              <BlockRenderer :template="tpl" :selectable="true" @select="(id) => (selectedNodeId = id)" />
            </div>
          </div>
        </main>

        <!-- Right: Form -->
        <section v-if="showRight" class="col-span-3">
          <div class="rounded border bg-white">
            <div class="px-3 py-2 font-medium border-b flex items-center justify-between">
              <div>Block Settings</div>
              <button class="text-red-600 text-sm px-2" @click="removeSelected" :disabled="!selectedNode || selectedNodeId==='ROOT'">Remove</button>
            </div>
            <div class="p-3 space-y-3" v-if="selectedNode">
              <DynamicForm :node="selectedNode" :block="findBlock(selectedNode.type)" @update:node="onUpdateNode" />
            </div>
            <div class="p-3 text-sm text-gray-500" v-else>Select a block to edit its settings.</div>
            <!-- Bottom Save Button -->
            <div class="px-3 py-3 border-t flex justify-end">
              <button
                class="rounded bg-primary px-4 py-2 text-white inline-flex items-center gap-2 disabled:opacity-60"
                @click="saveAndPreviewAll"
                :disabled="isSaving"
              >
                <svg v-if="isSaving" class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                </svg>
                <span>{{ isSaving ? 'Saving…' : 'Save' }}</span>
              </button>
            </div>
          </div>
        </section>
      </div>
    </div>
  </AppLayout>
</template>

