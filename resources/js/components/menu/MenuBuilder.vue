<script setup lang="ts">
import { ref, watch } from 'vue'
import Draggable from 'vuedraggable'
import MenuItemForm from './MenuItemForm.vue'

export interface MenuItem {
  id: number | string
  name: string
  url?: string
  item_model?: string
  model_id?: number
  target?: '_blank' | '_self'
  class?: string
  icon?: string
  children?: MenuItem[]
  mega_menu?: { enabled: boolean; columns: number }
  custom_fields?: Record<string, any>
}

const props = defineProps<{ modelValue: MenuItem[] }>()
const emit = defineEmits<{ (e: 'update:modelValue', value: MenuItem[]): void }>()

const items = ref<MenuItem[]>(props.modelValue || [])
const openIds = ref<Set<MenuItem['id']>>(new Set())

function toggleOpen(id: MenuItem['id']) {
  const s = new Set(openIds.value)
  if (s.has(id)) s.delete(id)
  else s.add(id)
  openIds.value = s
}

watch(() => props.modelValue, (val) => {
  items.value = Array.isArray(val) ? [...val] : []
})

watch(items, (val) => {
  emit('update:modelValue', val)
}, { deep: true })
</script>

<template>
  <div class="menu-builder grid grid-cols-1 gap-6">
    <div class="content-panel p-4 border rounded bg-white">
      <p class="text-sm text-gray-600 mb-3">Drag top-level items to reorder. Nested support coming next.</p>
      <Draggable v-model="items" :animation="200" handle=".drag-handle" item-key="id" class="space-y-2">
        <!-- @vue-ignore: slot typing from vuedraggable -->
        <template #item="{ element, index }">
          <div class="draggable-item bg-white border rounded">
            <!-- Header row -->
            <div class="flex items-center gap-3 px-3 py-2 bg-gray-100 rounded-t">
              <span class="drag-handle cursor-move select-none text-gray-500">⋮⋮</span>
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-800">{{ element.name }}</div>
              </div>
              <button type="button" class="flex items-center text-left" @click="toggleOpen(element.id)">
                <span class="text-xs px-3 py-1 rounded border bg-white text-gray-700">
                  {{ element.item_model === 'custom' ? 'Custom' : (element.item_model?.split('\\').pop() || 'Item') }}
                  <i class="fa ml-2 text-gray-500" :class="openIds.has(element.id) ? 'fa-caret-up' : 'fa-caret-down'"></i>
                </span>
              </button>
              <button type="button" class="ml-2 px-2 py-1 text-xs rounded border bg-white" @click="items.splice(index, 1)">Remove</button>
            </div>

            <!-- Collapsible body inside same card -->
            <div v-show="openIds.has(element.id)" class="p-3">
              <MenuItemForm v-model="items[index]" @delete="items.splice(index, 1)" />
            </div>
          </div>
        </template>
      </Draggable>
    </div>
  </div>

</template>
