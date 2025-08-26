<script setup lang="ts">
import { ref, watch } from 'vue'
import RichTextEditor from '@/components/RichTextEditor.vue'
import ImagePicker from '@/components/filemanager/ImagePicker.vue'

interface Props {
  modelValue: any
  field: {
    id: number
    label: string
    key: string
    type: 'input' | 'textarea' | 'file' | 'ck_editor'
    meta?: Record<string, any> | null
  }
}

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue'])
const local = ref<any>(props.modelValue)

watch(() => props.modelValue, (v) => (local.value = v))
watch(local, (v) => emit('update:modelValue', v))
</script>

<template>
  <div class="space-y-1">
    <label class="block text-sm font-medium text-gray-700">{{ field.label }}</label>

    <input v-if="field.type === 'input'" v-model="local" type="text" :placeholder="field.meta?.placeholder || ''"
      class="w-full rounded border px-3 py-2" />

    <textarea v-else-if="field.type === 'textarea'" v-model="local" rows="4"
      :placeholder="field.meta?.placeholder || ''" class="w-full rounded border px-3 py-2"></textarea>

    <div v-else-if="field.type === 'file'" class="max-w-xs">
      <ImagePicker v-model="local" :name="field.key" :placeholder="field.meta?.placeholder || '/storage/placeholder.png'" />
    </div>

    <RichTextEditor v-else-if="field.type === 'ck_editor'" v-model="local" />
  </div>
</template>
