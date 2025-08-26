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
const csrf = computed(() => {
  const fromPage = (page.props as any)?.csrf_token;
  if (fromPage) return fromPage as string;
  const meta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
  return meta?.content || '';
});

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const form = useForm<{ image: File | null; upload_from: string; _token: string }>({
  image: null,
  upload_from: 'verification',
  _token: csrf.value
});

// Flow state
const uploaded = ref(false);
const imageUrl = ref<string | null>(null);
const filePath = ref<string | null>(null);
const processing = ref(false);
const result = ref<any | null>(null);
const errorMsg = ref<string | null>(null);

// analyse-style processing UI state
const analyseGenerationProgress = ref(0);
const analyseGenerationStatus = ref('Starting analysis…');
const intervalRef = ref<number | null>(null);

// Steps (used for both UI display and status updates)
const analyseSteps = [
  { emoji: '👁️', label: 'Analyzing visual data…' },
  { emoji: '🔠', label: 'Running OCR…' },
  { emoji: '🗂️', label: 'Matching policy records…' },
  { emoji: '🛡️', label: 'Evaluating fraud signals…' },
  { emoji: '✅', label: 'Finalizing results…' },
];
const stepColors = ['#3b82f6', '#10b981', '#f59e0b', '#a855f7', '#ef4444'];

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

async function submit() {
  if (!form.image) return;
  errorMsg.value = null;
  try {
    const fd = new FormData();
    fd.append('image', form.image);
    fd.append('upload_from', form.upload_from);
    const resp = await fetch(route('app.verification.upload'), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf.value },
      body: fd,
    });
    if (!resp.ok) throw new Error('Upload failed');
    const data = await resp.json();
    uploaded.value = true;
    imageUrl.value = data.url;
    filePath.value = data.path;
    // Reset input
    form.reset();
    if (fileInput.value) fileInput.value.value = '';

    // Start verification call with processing animation
    processing.value = true;
    analyseGenerationProgress.value = 0;
    analyseGenerationStatus.value = 'Uploading image…';

    // Progress simulation while waiting for backend (caps at 95%)
    let stepIdx = 0;
    intervalRef.value = window.setInterval(() => {
      if (analyseGenerationProgress.value < 95) {
        analyseGenerationProgress.value += Math.random() * 6 + 2; // 2–8%
        if (analyseGenerationProgress.value > 95) analyseGenerationProgress.value = 95;
      }
      analyseGenerationStatus.value = analyseSteps[Math.min(stepIdx, analyseSteps.length - 1)].label;
      stepIdx++;
    }, 700);
    const vresp = await fetch(route('app.verification.verify'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf.value,
      },
      body: JSON.stringify({ path: filePath.value }),
    });
    if (!vresp.ok) throw new Error('Verification failed');
    result.value = await vresp.json();
    analyseGenerationProgress.value = 100;
    analyseGenerationStatus.value = 'Completed';
  } catch (e: any) {
    errorMsg.value = e?.message || 'Something went wrong';
  } finally {
    // stop progress interval
    try { if (intervalRef.value) clearInterval(intervalRef.value); } catch {}
    processing.value = false;
    // Allow the UI to show 100% briefly
    setTimeout(() => {
      analyseGenerationProgress.value = 0;
      analyseGenerationStatus.value = 'Starting analysis…';
    }, 600);
  }
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

        <!-- Error banner -->
        <div v-if="errorMsg" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
          {{ errorMsg }}
        </div>

        <!-- Validation error banner (shows all errors from ValidationException) -->
        <div v-if="Object.keys(form.errors).length" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
          <ul class="list-disc pl-5 space-y-1">
            <li v-for="(message, field) in form.errors" :key="field">{{ message }}</li>
          </ul>
        </div>

        <div v-if="!uploaded"
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
          <div v-if="props.atLimit" class="mt-3 text-sm text-red-600">You've reached your quota.
            <a :href="upgradeUrl" class="underline">Upgrade your plan</a>.
        </div>
        </div>

        <!-- Preview and processing / result -->
        <div v-if="uploaded" class="space-y-6 relative">

          <!-- Preview -->
          <div class="flex items-center gap-4">
            <img v-if="imageUrl" :src="imageUrl" alt="Uploaded" class="h-32 w-32 rounded border object-cover" />
          </div>

          <!-- Processing overlay -->

          <div v-if="processing" class="relative">
            <div class="analyse-processing-overlay">
              <div class="analyse-processing-content">
                <div class="analyse-processing-animation">
                  <div class="analyse-icon">📷</div>
                  <div class="analyse-scanning-beam"></div>
                  <div class="analyse-pulse-rings">
                    <div class="pulse-ring ring-1"></div>
                    <div class="pulse-ring ring-2"></div>
                    <div class="pulse-ring ring-3"></div>
                  </div>
                </div>
                <div class="analyse-processing-text">
                  <h3>Insurance Verification</h3>
                  <p class="processing-message">{{ analyseGenerationStatus }}</p>
                  <div class="progress-bar-container">
                    <div class="progress-bar-fill" :style="{ width: analyseGenerationProgress + '%' }">
                      <span class="progress-text">{{ Math.round(analyseGenerationProgress) }}%</span>
                    </div>
                  </div>
                </div>
                <div class="analyse-processing-stats">
                  <div v-for="(s, i) in analyseSteps" :key="i" class="stat-item" :class="{ active: s.label === analyseGenerationStatus }">
                    <span class="icon-badge" :style="{ backgroundColor: stepColors[i % stepColors.length] }">{{ s.emoji }}</span>
                    <span>{{ s.label.replace('…','') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Result card -->
          <div v-if="result" class="rounded-xl border bg-white p-6 shadow-sm dark:bg-zinc-900">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold">Insurance Verification</h2>
              <span :class="['rounded px-2 py-0.5 text-xs', result.verified ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800']">
                {{ result.verified ? 'Verified' : 'Not verified' }}
              </span>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
              <div><div class="text-xs text-muted-foreground">Policy #</div><div class="font-medium">{{ result.policy.policy_number }}</div></div>
              <div><div class="text-xs text-muted-foreground">Provider</div><div class="font-medium">{{ result.policy.provider }}</div></div>
              <div><div class="text-xs text-muted-foreground">Insured</div><div class="font-medium">{{ result.policy.insured_name }}</div></div>
              <div><div class="text-xs text-muted-foreground">Coverage</div><div class="font-medium">{{ result.policy.coverage }}</div></div>
              <div><div class="text-xs text-muted-foreground">Effective</div><div class="font-medium">{{ result.policy.effective_date }}</div></div>
              <div><div class="text-xs text-muted-foreground">Expires</div><div class="font-medium">{{ result.policy.expiration_date }}</div></div>
              <div><div class="text-xs text-muted-foreground">Premium</div><div class="font-medium">{{ result.policy.premium }} {{ result.policy.currency }}</div></div>
              <div><div class="text-xs text-muted-foreground">Status</div><div class="font-medium">{{ result.policy.status }}</div></div>
            </div>
            <div class="mt-6">
              <div class="text-sm font-medium">Checks</div>
              <ul class="mt-2 grid gap-2 sm:grid-cols-2">
                <li v-for="(c, i) in result.checks" :key="i" class="flex items-center gap-2 text-sm">
                  <span class="h-2 w-2 rounded-full" :class="c.status === 'passed' || c.status === 'clear' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                  {{ c.label }} — <span class="text-muted-foreground">{{ c.status }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </CustomerLayout>
  </component>
</template>

<style scoped>
.analyse-processing-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(240,248,255,0.95));
  backdrop-filter: blur(10px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10;
  border-radius: 0.75rem;
  animation: fadeIn 0.4s ease-in-out forwards;
}
.analyse-processing-content { text-align: center; color: #334155; width: 80%; padding: 24px; }
.analyse-processing-animation { position: relative; width: 120px; height: 120px; margin: 0 auto 20px; }
.analyse-icon { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 40px; animation: imageFloat 3s ease-in-out infinite; }
.analyse-scanning-beam { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(56,189,248,0.35), transparent); animation: scanningBeam 2s infinite ease-in-out; border-radius: 9999px; }
.analyse-pulse-rings { position: absolute; inset: 0; display: grid; place-items: center; }
.pulse-ring { position: absolute; border: 2px solid rgba(56,189,248,0.45); border-radius: 9999px; animation: pulse 2.2s ease-out infinite; }
.pulse-ring.ring-1 { width: 120px; height: 120px; }
.pulse-ring.ring-2 { width: 160px; height: 160px; animation-delay: 0.4s; }
.pulse-ring.ring-3 { width: 200px; height: 200px; animation-delay: 0.8s; }
.analyse-processing-text h3 { font-weight: 700; font-size: 1.25rem; margin-bottom: 8px; background: linear-gradient(135deg, #06b6d4, #10b981); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
.processing-message { font-size: 0.9rem; color: #64748b; }
.progress-bar-container { width: 100%; height: 18px; background-color: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-top: 12px; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #06b6d4, #10b981); display: flex; align-items: center; justify-content: center; transition: width 0.4s ease; color: white; font-weight: 600; font-size: 0.8rem; }
.progress-text { text-shadow: 0 1px 2px rgba(0,0,0,0.25); }
.analyse-processing-stats { display: flex; justify-content: space-between; gap: 12px; margin-top: 16px; }
.analyse-processing-stats .stat-item { display: flex; flex-direction: column; align-items: center; gap: 6px; font-size: 0.85rem; color: #475569; transition: transform 0.2s ease; text-align: center; }
.analyse-processing-stats .stat-item.active { color: #0ea5e9; font-weight: 600; transform: translateY(-2px); }
.icon-badge { display: inline-flex; width: 28px; height: 28px; border-radius: 9999px; align-items: center; justify-content: center; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }

@keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
@keyframes imageFloat { 0%, 100% { transform: translate(-50%, -50%) scale(1) } 50% { transform: translate(-50%, -50%) scale(1.06) } }
@keyframes scanningBeam { 0% { transform: translateX(-100%) } 100% { transform: translateX(100%) } }
@keyframes pulse { 0% { transform: scale(0.85); opacity: 0.7 } 100% { transform: scale(1.15); opacity: 0 } }
</style>
