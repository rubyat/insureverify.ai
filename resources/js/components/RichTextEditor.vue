<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Ref } from 'vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
import type { Editor, EditorConfig } from '@ckeditor/ckeditor5-core'

type UploadConfig = EditorConfig & {
  simpleUpload?: {
    uploadUrl: string
    headers?: Record<string, string>
  }
}

const props = defineProps<{
  modelValue?: string
  uploadUrl?: string | null
  placeholder?: string
  minHeight?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'ready'): void
}>()

const data: Ref<string> = ref(props.modelValue ?? '')

const csrf = document
  .querySelector('meta[name="csrf-token"]')
  ?.getAttribute('content') ?? ''

const config: UploadConfig = {
  placeholder: props.placeholder ?? '',
  ...(props.uploadUrl
    ? {
        simpleUpload: {
          uploadUrl: props.uploadUrl,
          headers: {
            'X-CSRF-TOKEN': csrf,
            Accept: 'application/json',
          },
        },
      }
    : {}),
}

watch(
  () => props.modelValue,
  (v) => {
    if ((v ?? '') !== data.value) data.value = v ?? ''
  }
)

const onChange = (...args: unknown[]) => {
  const editor = args[1] as Editor
  emit('update:modelValue', editor.getData())
}
const onReady = () => emit('ready')
</script>

<template>
  <div class="ck-content-wrapper" :style="{ minHeight }">
    <ckeditor
      :editor="ClassicEditor"
      v-model="data"
      :config="config"
      @change="onChange"
      @ready="onReady"
    />
  </div>
</template>

<style>
.ck-content-wrapper .ck-editor__editable_inline { min-height: 180px; }
</style>
