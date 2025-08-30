<script setup lang="ts">
import { reactive, watch } from 'vue'

const props = defineProps<{ modelValue: any }>()
const emit = defineEmits<{ (e: 'update:modelValue', value: any): void; (e: 'delete'): void }>()

const local = reactive<any>({})

function assignFrom(src: any) {
  const v = src ?? {}
  // reset keys
  for (const k of Object.keys(local)) delete (local as any)[k]
  // assign incoming
  Object.assign(local, JSON.parse(JSON.stringify(v)))
}

assignFrom(props.modelValue)

watch(() => props.modelValue, (v) => assignFrom(v), { deep: true })
watch(local, (v) => emit('update:modelValue', JSON.parse(JSON.stringify(v))), { deep: true })
</script>

<template>
  <div class="space-y-3">
    <div>
      <label class="block text-sm text-gray-700 mb-1">Label</label>
      <input v-model="local.name" type="text" class="w-full border rounded px-3 py-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm text-gray-700 mb-1">Class</label>
        <input v-model="local.class" type="text" class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-gray-700 mb-1">Target</label>
        <select v-model="local.target" class="w-full border rounded px-3 py-2">
          <option value="_self">Normal</option>
          <option value="_blank">New tab</option>
        </select>
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-700 mb-1">Icon <a href="https://fontawesome.com/v4/icons/" target="_blank" class="text-blue-600 underline">(FontAwesome Icon Library)</a></label>
      <input v-model="local.icon" type="text" class="w-full border rounded px-3 py-2" placeholder="fa fa-folder-o" />
    </div>

    <div class="flex items-center justify-between">
      <button type="button" class="text-red-600" @click="emit('delete')">Delete</button>
    </div>
  </div>
</template>
