<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import type { Ref } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const props = defineProps<{
  modelValue?: string
  placeholder?: string
  minHeight?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'ready'): void
}>()

const data: Ref<string> = ref(props.modelValue ?? '')
const minHeightStyle = computed(() => props.minHeight ?? '180px')

watch(
  () => props.modelValue,
  (v) => {
    if ((v ?? '') !== data.value) data.value = v ?? ''
  }
)

watch(
  () => data.value,
  (v) => {
    emit('update:modelValue', v ?? '')
  }
)

const modules = {
  toolbar: [
    [{ header: [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ script: 'sub' }, { script: 'super' }],
    [{ indent: '-1' }, { indent: '+1' }],
    [{ align: [] as string[] }],
    ['blockquote', 'code-block'],
    ['link', 'image'],
    [{ color: [] as string[] }, { background: [] as string[] }],
    ['clean'],
  ],
}

const formats = [
  'header', 'bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block',
  'list', 'bullet', 'script', 'indent', 'align', 'link', 'image', 'color', 'background'
]

const onReady = () => emit('ready')
</script>

<template>
  <div class="quill-wrapper">
    <QuillEditor
      v-model:content="data"
      contentType="html"
      theme="snow"
      :modules="modules"
      :formats="formats"
      :placeholder="props.placeholder || ''"
      @ready="onReady"
      :style="{ minHeight: minHeightStyle }"
    />
  </div>
</template>

<style>
.quill-wrapper .ql-editor { min-height: 180px; }
</style>
