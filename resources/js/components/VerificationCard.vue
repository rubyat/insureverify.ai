<template>
  <div 
    class="border-2 border-dashed rounded-lg p-6 transition-colors"
    :class="{
      'border-primary bg-primary/5': isDragging,
      'border-muted': !isDragging,
      'opacity-50': disabled
    }"
    @dragover.prevent="onDragOver"
    @dragleave="onDragLeave"
    @drop="onDrop"
  >
    <div class="text-center space-y-4">
      <!-- Icon -->
      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
        <i :class="['fas', icon, 'text-primary text-2xl']"></i>
      </div>

      <!-- Title and instructions -->
      <div class="space-y-1">
        <h3 class="text-lg font-medium">{{ title }}</h3>
        <p class="text-sm text-muted-foreground">
          <button 
            type="button" 
            class="font-medium text-primary hover:text-primary/80 focus:outline-none"
            @click="onChooseFile"
            :disabled="disabled"
          >
            Upload a file
          </button>
          <span> or drag and drop</span>
        </p>
        <p class="text-xs text-muted-foreground">
          {{ acceptText }}
        </p>
      </div>

      <!-- File input (hidden) -->
      <input 
        ref="fileInput" 
        type="file" 
        class="hidden" 
        :accept="accept"
        @change="onFileChanged" 
      />

      <!-- Preview -->
      <div v-if="previewUrl" class="mt-4">
        <div class="relative group">
          <img 
            :src="previewUrl" 
            :alt="`Preview of ${file?.name || 'selected file'}" 
            class="mx-auto max-h-48 rounded border object-contain"
          />
          <button
            v-if="!disabled"
            @click="removeFile"
            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity"
            title="Remove file"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
        <p class="mt-2 text-sm text-muted-foreground truncate">
          {{ file?.name }}
        </p>
      </div>

      <!-- Progress bar -->
      <div v-if="progress > 0" class="pt-2">
        <div class="h-2 w-full bg-muted rounded-full overflow-hidden">
          <div 
            class="h-full bg-primary transition-all duration-300 rounded-full"
            :style="{ width: `${progress}%` }"
          ></div>
        </div>
        <p class="mt-1 text-xs text-muted-foreground text-right">
          Uploading... {{ progress }}%
        </p>
      </div>

      <!-- Error message -->
      <div v-if="error" class="mt-2 text-sm text-red-500">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, defineProps, defineEmits, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: File,
    default: null
  },
  title: {
    type: String,
    required: true
  },
  accept: {
    type: String,
    default: 'image/*,.pdf'
  },
  acceptText: {
    type: String,
    default: 'PNG, JPG, or PDF (max 5MB)'
  },
  icon: {
    type: String,
    default: 'fa-upload'
  },
  progress: {
    type: Number,
    default: 0
  },
  error: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'update:progress', 'update:error']);

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const previewUrl = ref('');

const file = computed({
  get() {
    return props.modelValue;
  },
  set(value: File | null) {
    emit('update:modelValue', value);
  }
});

// Generate preview URL when file changes
watch(() => props.modelValue, (newFile) => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = '';
  }

  if (newFile && newFile.type.startsWith('image/')) {
    previewUrl.value = URL.createObjectURL(newFile);
  }
}, { immediate: true });

function onChooseFile() {
  fileInput.value?.click();
}

function onFileChanged(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    const selectedFile = target.files[0];
    file.value = selectedFile;
    emit('update:error', '');
  } else {
    file.value = null;
  }
}

function removeFile() {
  if (fileInput.value) {
    fileInput.value.value = '';
  }
  file.value = null;
}

function onDragOver(event: DragEvent) {
  event.preventDefault();
  if (!props.disabled) {
    isDragging.value = true;
  }
}

function onDragLeave() {
  isDragging.value = false;
}

function onDrop(event: DragEvent) {
  event.preventDefault();
  isDragging.value = false;
  
  if (props.disabled) return;
  
  const files = event.dataTransfer?.files;
  if (files && files.length > 0) {
    const droppedFile = files[0];
    // Check file type
    if (props.accept && !props.accept.includes(droppedFile.type.split('/')[1])) {
      emit('update:error', 'Invalid file type');
      return;
    }
    file.value = droppedFile;
    emit('update:error', '');
  } else {
    file.value = null;
  }
}

// Clean up object URLs when component is unmounted
onUnmounted(() => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }
});
</script>
