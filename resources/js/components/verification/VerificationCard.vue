<template>
  <div class="border rounded-lg p-4 transition-all duration-300 hover:border-primary/50" :class="{ 'border-primary/30': isDragging, 'border-muted': !isDragging }">
    <div class="flex flex-col items-center justify-center space-y-4 py-6" @dragover.prevent="onDragOver" @dragleave="onDragLeave" @drop="onDrop">
      <input type="file" ref="fileInput" class="hidden" @change="onFileChange" accept="image/*" />
      
      <!-- Upload area -->
      <div v-if="!previewUrl" class="text-center cursor-pointer" @click="onClick">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
          <i class="fas fa-cloud-upload-alt text-primary text-xl"></i>
        </div>
        <p class="mt-2 text-sm text-muted-foreground">
          <span class="font-medium text-primary">Click to upload</span> or drag and drop
        </p>
        <p class="text-xs text-muted-foreground">{{ acceptText || 'PNG, JPG, JPEG' }} (max 5MB)</p>
      </div>
      
      <!-- Preview -->
      <div v-else class="relative w-full">
        <img :src="previewUrl" alt="Preview" class="mx-auto max-h-64 rounded-md object-contain" />
        <button 
          @click="removeImage" 
          class="absolute -right-2 -top-2 rounded-full bg-destructive p-1 text-white hover:bg-destructive/90"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <p class="text-sm font-medium">{{ title }}</p>
      
      <!-- Progress bar -->
      <div v-if="isUploading" class="w-full">
        <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
          <div 
            class="h-full bg-primary transition-all duration-300"
            :style="{ width: `${progress}%` }"
          ></div>
        </div>
        <p class="mt-1 text-xs text-muted-foreground">{{ statusText }}</p>
      </div>
      
      <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  acceptText: {
    type: String,
    default: '',
  },
  modelValue: {
    type: File,
    default: null,
  },
  isUploading: {
    type: Boolean,
    default: false,
  },
  progress: {
    type: Number,
    default: 0,
  },
  statusText: {
    type: String,
    default: 'Uploading...',
  },
  error: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue', 'upload']);

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const previewUrl = ref('');

// Watch for modelValue changes to update preview
watch(() => props.modelValue, (newFile) => {
  if (newFile) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewUrl.value = e.target?.result as string;
    };
    reader.readAsDataURL(newFile);
  } else {
    previewUrl.value = '';
  }
}, { immediate: true });

function onClick() {
  fileInput.value?.click();
}

function onFileChange(e: Event) {
  const target = e.target as HTMLInputElement;
  const file = target.files?.[0] || null;
  if (file) {
    emit('update:modelValue', file);
  }
}

function onDragOver(e: DragEvent) {
  e.preventDefault();
  isDragging.value = true;
}

function onDragLeave() {
  isDragging.value = false;
}

function onDrop(e: DragEvent) {
  e.preventDefault();
  isDragging.value = false;
  
  const file = e.dataTransfer?.files[0];
  if (file && file.type.startsWith('image/')) {
    emit('update:modelValue', file);
  }
}

function removeImage() {
  emit('update:modelValue', null);
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}
</script>
