<script setup lang="ts">
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue'
import type { Ref } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'
import ImagePicker from '@/components/filemanager/ImagePicker.vue'

const props = defineProps<{
  modelValue?: string
  placeholder?: string
  minHeight?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'ready'): void
}>()

const minHeightStyle = computed(() => props.minHeight ?? '180px')



const modules: any = {
  toolbar: {
    container: [
      [{ header: [1, 2, 3, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      [{ script: 'sub' }, { script: 'super' }],
      [{ indent: '-1' }, { indent: '+1' }],
      ['blockquote', 'code-block'],
      ['link', 'image'],
      ['clean'],
    ],
    handlers: {
      image: () => {
        showImagePicker.value = true;
      },
    },
  },
};

const formats = [
  'header', 'bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block',
  'list', 'bullet', 'script', 'indent', 'link', 'image'
]


// --- Image Picker Integration ---
const showImagePicker = ref(false)


const onImageSelected = (url: string | undefined) => {
  if (url && quillInstance) {
    const range = quillInstance.getSelection(true)
    quillInstance.insertEmbed(range.index, 'image', '/storage/catalog/' + url)
    quillInstance.setSelection(range.index + 1, 0)
  }
  showImagePicker.value = false
}

let quillInstance: Quill | null = null
const editorContainer = ref<HTMLDivElement | null>(null)

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting && editorContainer.value && !quillInstance) {
        quillInstance = new Quill(editorContainer.value, {
          theme: 'snow',
          modules,
          formats,
          placeholder: props.placeholder || '',
        })

        // Set initial content
        if (props.modelValue) {
          quillInstance.root.innerHTML = props.modelValue
        }

        // Listen for changes
        quillInstance.on('text-change', () => {
          if (quillInstance) {
            emit('update:modelValue', quillInstance.root.innerHTML)
          }
        })

        // Stop observing once initialized
        observer.disconnect()
        emit('ready')
      }
    },
    { threshold: 0.1 }
  )

  if (editorContainer.value) {
    observer.observe(editorContainer.value)
  }

  onBeforeUnmount(() => {
    observer.disconnect()
    if (quillInstance) {
      quillInstance = null
    }
  })
})

// Watch for parent changes to update the editor
watch(
  () => props.modelValue,
  (newValue) => {
    if (quillInstance && newValue !== quillInstance.root.innerHTML) {
      quillInstance.root.innerHTML = newValue || ''
    }
  }
)

</script>

<template>
  <div class="quill-wrapper">
    <div v-if="showImagePicker" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="showImagePicker = false"></div>
    <div v-if="showImagePicker" class="fixed inset-y-0 right-0 w-full max-w-4xl bg-white shadow-xl z-50 transform transition-transform duration-300 ease-in-out">
      <ImagePicker @update:modelValue="onImageSelected" @close="showImagePicker = false" :modal="true" />
    </div>
        <div ref="editorContainer" :style="{ minHeight: minHeightStyle }"></div>
  </div>
</template>

<style>
.quill-wrapper .ql-editor { min-height: 180px; }
</style>
