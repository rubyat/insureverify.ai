<template>
  <div class="max-w-[200px] border rounded overflow-hidden">
    <img :src="thumbToShow" :alt="''" class="w-full object-contain" />
    <div class="grid grid-cols-2">
      <button type="button" class="px-3 py-2 text-sm rounded-none border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50" @click="open = true">
        <i class="fa-solid fa-pencil" /> Edit
      </button>
      <button type="button" class="px-3 py-2 text-sm rounded-none border border-transparent bg-yellow-500 text-white hover:bg-yellow-600 disabled:opacity-50" @click="clearValue">
        <i class="fa-regular fa-trash-can" /> Clear
      </button>
    </div>
    <input type="hidden" :name="name" :value="modelValue" />
  </div>

  <FileManagerModal v-model="open" @picked="onPicked" />
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import FileManagerModal from './FileManagerModal.vue'

const props = defineProps<{
  name?: string
  modelValue?: string
  placeholder?: string
  baseUrl?: string // optional: absolute url to prepend if modelValue is relative
  thumb?: string // optional: explicit preview thumb to show initially
}>()

const emit = defineEmits<{ (e: 'update:modelValue', v: string | undefined): void }>()

const open = ref(false)
const previewThumb = ref<string | undefined>(props.thumb ?? undefined)

const thumbToShow = computed(() => {
  if (previewThumb.value) return previewThumb.value
  if (props.thumb) return props.thumb
  if (props.modelValue) {
    if (props.modelValue.startsWith('http')) return props.modelValue
    if (props.baseUrl) return props.baseUrl.replace(/\/$/, '') + '/' + props.modelValue.replace(/^\//, '')
  }
  return props.placeholder || '/storage/placeholder.png'
})

function onPicked(p: { path: string; href: string; name: string; thumb?: string | null }) {
  console.log('p', p)
  previewThumb.value = p.thumb ?? undefined
  emit('update:modelValue', props.baseUrl ? p.path : p.path)
}

function clearValue() {
  previewThumb.value = '/storage/placeholder.png'
  emit('update:modelValue', undefined)
}
</script>

<style scoped>
</style>
