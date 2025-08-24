<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
  remainingUploads: number;
  cycleResetDate: string;
  queue: Array<{ id: number; name: string; size: string; progress: number; error?: string }>;
  atLimit: boolean;
  upgradeUrl: string;
}>();

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Verification', href: '/app/verification' }];
const flash = computed(() => ((page.props as any)?.flash ?? {}));

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const form = useForm<{ image: File | null; upload_from: string; _token: string }>({
  image: null,
  upload_from: 'verification',
  _token: (page.props as any)?.csrf_token || ''
});

function onChooseFile() {
  fileInput.value?.click();
}

function onFileChanged(e: Event) {
  const target = e.target as HTMLInputElement;
  const file = target.files?.[0] || null;
  form.image = file;
  // Auto-upload on file choose
  if (form.image && !props.atLimit) submit();
}

function submit() {
  if (!form.image) return;
  console.log(form);
  form.post(route('app.verification.upload'), {
    preserveScroll: true,
    forceFormData: true,
    onProgress: () => {
      // Inertia useForm will also populate form.progress
    },
    onSuccess: () => {
      form.reset();
      if (fileInput.value) fileInput.value.value = '';
    },
  });
}

function onDrop(e: DragEvent) {
  e.preventDefault();
  isDragging.value = false;
  const dt = e.dataTransfer;
  if (!dt || !dt.files || !dt.files.length) return;
  const file = dt.files[0];
  form.image = file;
  if (form.image && !props.atLimit) submit();
}

function onDragOver(e: DragEvent) {
  e.preventDefault();
  isDragging.value = true;
}

function onDragLeave() {
  isDragging.value = false;
}
</script>

<template>
  <Head title="Verification" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Verification</h1>
          <div class="text-sm text-muted-foreground">Remaining: {{ remainingUploads }} · Resets {{ cycleResetDate }}</div>
        </div>

        <!-- Success banner and preview -->
        <div v-if="flash.success" class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800">
          {{ flash.success }}
        </div>
        <div v-if="flash.uploaded_image_url" class="mt-2">
          <img :src="flash.uploaded_image_url" alt="Uploaded preview" class="max-h-56 rounded border" />
        </div>

        <!-- Validation error banner (shows all errors from ValidationException) -->
        <div v-if="Object.keys(form.errors).length" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
          <ul class="list-disc pl-5 space-y-1">
            <li v-for="(message, field) in form.errors" :key="field">{{ message }}</li>
          </ul>
        </div>

        <div
          :class="['flex flex-col items-center justify-center rounded border-2 border-dashed p-10 text-center transition-colors', props.atLimit ? 'opacity-60' : '', isDragging ? 'border-primary bg-primary/5' : '']"
          @dragover="onDragOver"
          @dragleave="onDragLeave"
          @drop="onDrop"
        >
          <div class="text-lg font-medium">Upload a photo for verification</div>
          <div class="mt-1 text-sm text-muted-foreground">JPEG, PNG up to 5 MB</div>

          <form class="mt-4" @submit.prevent="submit">
            <!-- Hidden metadata for non-programmatic submits; Inertia will send from form state -->
            <input type="hidden" name="upload_from" :value="form.upload_from" />
            <input type="hidden" name="_token" :value="form._token" />
            <input ref="fileInput" class="hidden" type="file" accept="image/*" @change="onFileChanged" />


            <div class="flex items-center gap-2">
              <button type="button" class="rounded-md bg-primary px-4 py-2 text-white" :disabled="props.atLimit" @click="onChooseFile">
                Choose file
              </button>
            </div>

            <!-- Drag and drop helper text -->
            <div class="mt-2 text-xs text-muted-foreground">or drag & drop an image here</div>

            <!-- Progress bar -->
            <div v-if="form.progress" class="mt-4 w-full">
              <div class="h-2 w-full rounded bg-gray-200">
                <div
                  class="h-2 rounded bg-green-500 transition-all"
                  :style="{ width: `${form.progress.percentage}%` }"
                />
              </div>
              <div class="mt-1 text-xs text-muted-foreground">Uploading {{ form.progress.percentage }}%</div>
            </div>
          </form>

          <div v-if="form.errors.image" class="mt-3 text-sm text-red-600">{{ form.errors.image }}</div>
          <div v-if="props.atLimit" class="mt-3 text-sm text-red-600">You've reached your quota. <a :href="upgradeUrl" class="underline">Upgrade your plan</a>.</div>
        </div>
      </div>
    </CustomerLayout>
  </component>
</template>
