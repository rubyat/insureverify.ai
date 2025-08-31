<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, nextTick } from 'vue';
import VerificationCard from '@/components/verification/VerificationCard.vue';

type VerificationType = 'license' | 'insurance' | 'face';

interface VerificationProps {
    remainingUploads: number;
    cycleResetDate?: string | null;
    atLimit?: boolean;
}

// Use the props in the component
const { remainingUploads, cycleResetDate } = defineProps<VerificationProps>();

// Local reactive copy so we can update the header immediately after verify
const remaining = ref<number>(remainingUploads);

// Tab state
const activeTab = ref<VerificationType>('license');
const tabs = [
    { id: 'license', name: 'License Verification', icon: 'fa-id-card' },
    { id: 'insurance', name: 'Insurance Verification', icon: 'fa-file-contract' },
    { id: 'face', name: 'Face Verification', icon: 'fa-user' },
] as const;

// Form state
type VerificationForm = {
    type: VerificationType;
    licenseFront?: File;
    licenseBack?: File;
    insuranceDoc?: File;
    faceImage?: File;
}

const form = useForm<VerificationForm>({
    type: 'license',
    licenseFront: undefined,
    licenseBack: undefined,
    insuranceDoc: undefined,
    faceImage: undefined,
});


// UI state
const isUploading = ref<Record<VerificationType, boolean>>({
    license: false,
    insurance: false,
    face: false
});

const uploadStatus = ref<Record<VerificationType, { progress: number; status: 'idle' | 'uploading' | 'success' | 'error' }>>({
    license: { progress: 0, status: 'idle' },
    insurance: { progress: 0, status: 'idle' },
    face: { progress: 0, status: 'idle' }
});

const isAnalyzing = ref(false);
const errorMsg = ref('');
const analysisResult = ref<any>(null);
const processingSection = ref<HTMLElement | null>(null);
const resultsSection = ref<HTMLElement | null>(null);

// Analysis simulation
const analyseGenerationProgress = ref(0);
const analyseGenerationStatus = ref('Starting analysis...');
const analyseSteps = [
    { emoji: '📄', label: 'Uploading' },
    { emoji: '🔍', label: 'Analyzing' },
    { emoji: '🔢', label: 'Processing' },
    { emoji: '✅', label: 'Validating' },
    { emoji: '📊', label: 'Complete' },
];

const stepColors = [
    '#3b82f6', // blue-500
    '#8b5cf6', // violet-500
    '#ec4899', // pink-500
    '#f59e0b', // amber-500
    '#10b981', // emerald-500
];

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [
    { title: 'Dashboard', href: route('app.dashboard') },
    { title: 'Verification', href: route('app.verification') },
];

// Computed properties
const currentVerificationType = computed(() => {
    return tabs.find(tab => tab.id === activeTab.value)?.name || '';
});

const canVerify = computed(() => {
    switch (activeTab.value) {
        case 'license':
            return !!form.licenseFront && !!form.licenseBack;
        case 'insurance':
            return !!form.insuranceDoc;
        case 'face':
            return !!form.faceImage;
        default:
            return false;
    }
});

//

// Methods
function resetVerification() {
    analysisResult.value = null;
    const tab = activeTab.value;
    uploadStatus.value[tab] = { progress: 0, status: 'idle' };

    switch (tab) {
        case 'license':
            form.licenseFront = undefined;
            form.licenseBack = undefined;
            break;
        case 'insurance':
            form.insuranceDoc = undefined;
            break;
        case 'face':
            form.faceImage = undefined;
            break;
    }
    errorMsg.value = '';
}

function getCsrfToken(): string | null {
    const el = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    return el?.content ?? null;
}

function getActiveFile(): File | undefined {
    switch (activeTab.value) {
        case 'license':
            return form.licenseFront;
        case 'insurance':
            return form.insuranceDoc;
        case 'face':
            return form.faceImage;
        default:
            return undefined;
    }
}

async function uploadFile(): Promise<{ path: string }> {
    const file = getActiveFile();
    if (!file) throw new Error('No file selected');

    const fd = new FormData();
    fd.append('image', file);
    fd.append('upload_from', activeTab.value);

    const csrf = getCsrfToken();
    const res = await fetch(route('app.verification.upload'), {
        method: 'POST',
        headers: csrf ? { 'X-CSRF-TOKEN': csrf } : undefined,
        body: fd,
        credentials: 'same-origin',
    });
    if (!res.ok) {
        const text = await res.text();
        throw new Error(text || 'Upload failed');
    }
    const data = await res.json();
    return { path: data.path ?? data.main_path };
}
 
async function startAnalysisWithServer(path: string) {
    // Show processing UI
    isAnalyzing.value = true;
    analyseGenerationProgress.value = 0;

    // Begin server verification
    const csrf = getCsrfToken();
    const verifyPromise = fetch(route('app.verification.verify'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
        },
        body: JSON.stringify({ path }),
        credentials: 'same-origin',
    });

    // Local progress animation while waiting for server
    const anim = setInterval(() => {
        const target = 90;
        const inc = Math.random() * 5;
        if (analyseGenerationProgress.value < target) {
            analyseGenerationProgress.value = Math.min(target, analyseGenerationProgress.value + inc);
        }

        if (analyseGenerationProgress.value < 20) {
            analyseGenerationStatus.value = 'Uploading files...';
        } else if (analyseGenerationProgress.value < 40) {
            analyseGenerationStatus.value = 'Analyzing document...';
        } else if (analyseGenerationProgress.value < 60) {
            analyseGenerationStatus.value = 'Processing data...';
        } else if (analyseGenerationProgress.value < 80) {
            analyseGenerationStatus.value = 'Validating information...';
        } else {
            analyseGenerationStatus.value = 'Finalizing verification...';
        }
    }, 200);

    try {
        const res = await verifyPromise;
        if (!res.ok) throw new Error(await res.text());
        const payload = await res.json();

        // Finish progress and hide processing
        analyseGenerationProgress.value = 100;
        clearInterval(anim);
        isAnalyzing.value = false;

        // Map server payload to existing UI structure
        analysisResult.value = {
            success: !!payload?.verified,
            message: payload?.verified ? 'Verification completed successfully!' : 'Verification failed. Please try again.',
            details: {
                verification_id: payload?.policy?.policy_number ?? `VER-${Date.now()}`,
                date_verified: new Date().toLocaleDateString(),
                status: payload?.verified ? 'verified' : 'failed',
                confidence_score: '—',
            },
            ...payload,
        } as any;

        // Decrement remaining uploads locally
        remaining.value = Math.max(0, (remaining.value ?? 0) - 1);

        // Smooth scrolling
        setTimeout(() => {
            if (processingSection.value) {
                processingSection.value.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 200);
        nextTick(() => {
            if (resultsSection.value) {
                resultsSection.value.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    } catch (e) {
        clearInterval(anim);
        isAnalyzing.value = false;
        errorMsg.value = 'Verification failed. Please try again.';
        console.error(e);
    }
}

// Triggered by the "Verify Now" button
const verify = async () => {
    if (!canVerify.value || remaining.value <= 0) {
        errorMsg.value = 'You have reached your upload limit. Please upgrade your plan to continue.';
        return;
    }

    const tab = activeTab.value;
    isUploading.value[tab] = true;
    uploadStatus.value[tab] = { progress: 0, status: 'uploading' };

    try {
        // Drive visual upload progress while real upload runs
        let uploading = true;
        const prog = setInterval(() => {
            if (!uploading) return clearInterval(prog);
            uploadStatus.value[tab].progress = Math.min(95, uploadStatus.value[tab].progress + 7);
        }, 120);

        const { path } = await uploadFile();
        uploading = false;
        clearInterval(prog);
        uploadStatus.value[tab].progress = 100;
        uploadStatus.value[tab].status = 'success';

        // Begin analysis with server and scroll to processing section
        startAnalysisWithServer(path);
        setTimeout(() => {
            if (processingSection.value) {
                processingSection.value.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 500);
    } catch (error) {
        uploadStatus.value[tab] = { progress: 0, status: 'error' };
        errorMsg.value = 'An error occurred during upload. Please try again.';
        console.error('Upload/verify error:', error);
    } finally {
        isUploading.value[tab] = false;
    }
}
</script>

<template>
  <Head title="Verification" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div ref="resultsSection" class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-2xl font-semibold">Verification</h1>
            <p class="text-sm text-muted-foreground mt-1">Verify licenses, insurance documents, and identity</p>
          </div>
          <div class="flex items-center bg-muted/50 px-3 py-1.5 rounded-full text-sm">
            <i class="fas fa-sync-alt text-primary mr-2"></i>
            <span>Remaining: {{ remaining }}</span>
            <span class="mx-2 text-muted-foreground">•</span>
            <span>Resets {{ cycleResetDate }}</span>
          </div>
        </div>

        <!-- Error banner -->
        <div v-if="errorMsg" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
          {{ errorMsg }}
        </div>

        <!-- Main content -->
        <div v-if="!analysisResult" class="space-y-6">
          <!-- Tabs -->
          <div class="border-b border-muted">
            <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id as VerificationType"
                class="whitespace-nowrap border-b-2 py-4 px-3 text-sm font-medium transition-colors duration-200"
                :class="{
                  'border-primary text-primary': activeTab === tab.id,
                  'border-transparent text-muted-foreground hover:border-muted-foreground/50 hover:text-foreground': activeTab !== tab.id,
                  'pointer-events-none opacity-50': isUploading[tab.id] || isAnalyzing
                }"
              >
                <i :class="['fas', tab.icon, 'mr-2']"></i>
                {{ tab.name }}
              </button>
            </nav>
          </div>

          <!-- Tab content -->
          <div class="space-y-6">
            <!-- License Verification -->
            <div v-if="activeTab === 'license'" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <VerificationCard
                  v-model="form.licenseFront"
                  title="License Front"
                  icon="fa-id-card"
                  accept="image/*"
                  accept-text="JPG, PNG (max 5MB)"
                  :disabled="isUploading[activeTab] || isAnalyzing"
                  :progress="uploadStatus[activeTab].progress"
                  :error="uploadStatus[activeTab].status === 'error' ? 'Error uploading file' : ''"
                />
                <VerificationCard
                  v-model="form.licenseBack"
                  title="License Back"
                  icon="fa-id-card"
                  accept="image/*"
                  accept-text="JPG, PNG (max 5MB)"
                  :disabled="isUploading[activeTab] || isAnalyzing"
                  :progress="uploadStatus[activeTab].progress"
                  :error="uploadStatus[activeTab].status === 'error' ? 'Error uploading file' : ''"
                />
              </div>
              <p class="text-sm text-muted-foreground text-center">
                Please upload both front and back of your driver's license or ID card
              </p>
            </div>

            <!-- Insurance Verification -->
            <div v-else-if="activeTab === 'insurance'" class="space-y-6">
              <div class="max-w-2xl mx-auto">
                <VerificationCard
                  v-model="form.insuranceDoc"
                  title="Insurance Document"
                  icon="fa-file-contract"
                  accept="image/*,.pdf"
                  accept-text="PDF, JPG, PNG (max 10MB)"
                  :disabled="isUploading[activeTab] || isAnalyzing"
                  :progress="uploadStatus[activeTab].progress"
                  :error="uploadStatus[activeTab].status === 'error' ? 'Error uploading file' : ''"
                />
              </div>
              <p class="text-sm text-muted-foreground text-center">
                Upload a clear photo or scan of your insurance card or policy document
              </p>
            </div>

            <!-- Face Verification -->
            <div v-else-if="activeTab === 'face'" class="space-y-6">
              <div class="max-w-md mx-auto">
                <VerificationCard
                  v-model="form.faceImage"
                  title="Face Image"
                  icon="fa-user"
                  accept="image/*"
                  accept-text="JPG, PNG (max 5MB)"
                  :disabled="isUploading[activeTab] || isAnalyzing"
                  :progress="uploadStatus[activeTab].progress"
                  :error="uploadStatus[activeTab].status === 'error' ? 'Error uploading file' : ''"
                />
              </div>
              <p class="text-sm text-muted-foreground text-center">
                Make sure your face is clearly visible and well-lit. Remove any hats or sunglasses.
              </p>
            </div>

            <!-- Verify Button -->
            <div class="flex justify-center pt-4">
              <button
                @click="verify"
                :disabled="!canVerify || isUploading[activeTab] || isAnalyzing || remaining <= 0"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <i v-if="isUploading[activeTab] || isAnalyzing" class="fas fa-spinner fa-spin mr-2"></i>
                <i v-else-if="!isUploading[activeTab] && !isAnalyzing && remaining <= 0" class="fas fa-exclamation-triangle mr-2"></i>
                <i v-else class="fas fa-check-circle mr-2"></i>
                {{
                    isUploading[activeTab] ? 'Uploading...' :
                    isAnalyzing ? 'Processing...' :
                    remaining <= 0 ? 'Upload Limit Reached' : 'Verify Now'
                }}
              </button>
            </div>

            <!-- Upgrade Prompt -->
            <div v-if="remaining <= 0" class="mt-4 text-center">
                <p class="text-sm text-amber-600">
                    You've reached your upload limit.
                    <a :href="route('app.upgrade')" class="text-primary hover:underline font-medium">Upgrade your plan</a>
                    to continue verifying documents.
                </p>
            </div>
          </div>

          <!-- Analysis Progress -->
          <div v-if="isAnalyzing" ref="processingSection" class="space-y-6 pt-8">
            <div class="text-center">
              <h3 class="text-lg font-medium">Verifying {{ currentVerificationType }}</h3>
              <p class="text-muted-foreground mt-1">This may take a few moments...</p>
            </div>

            <!-- Progress bar -->
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span>Progress</span>
                <span class="font-medium">{{ Math.round(analyseGenerationProgress) }}%</span>
              </div>
              <div class="h-2 w-full bg-muted rounded-full overflow-hidden">
                <div
                  class="h-full transition-all duration-300 rounded-full"
                  :style="{
                    width: `${analyseGenerationProgress}%`,
                    background: `linear-gradient(90deg, ${stepColors[Math.min(Math.floor(analyseGenerationProgress / 20), stepColors.length - 1)]} 0%, ${stepColors[Math.min(Math.ceil(analyseGenerationProgress / 20), stepColors.length - 1)]} 100%)`
                  }"
                ></div>
              </div>
              <p class="text-sm text-muted-foreground text-center">
                {{ analyseGenerationStatus }}
              </p>
            </div>

            <!-- Processing steps -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-8">
              <div
                v-for="(step, index) in analyseSteps"
                :key="index"
                class="flex flex-col items-center text-center p-3 rounded-lg transition-colors"
                :class="{
                  'bg-primary/5 border border-primary/20': index * 20 <= analyseGenerationProgress,
                  'opacity-50': index * 20 > analyseGenerationProgress
                }"
              >
                <span class="text-2xl mb-2">{{ step.emoji }}</span>
                <span class="text-xs font-medium">{{ step.label }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Results -->
        <div v-else ref="resultsSection" class="space-y-6">
          <div
            :class="{
              'bg-green-50 border-l-4 border-green-400': analysisResult.success,
              'bg-red-50 border-l-4 border-red-400': !analysisResult.success
            }"
            class="p-6 rounded-r-md"
          >
            <div class="flex">
              <div class="flex-shrink-0">
                <i
                  :class="{
                    'fas fa-check-circle text-green-400 text-2xl': analysisResult.success,
                    'fas fa-times-circle text-red-400 text-2xl': !analysisResult.success
                  }"
                ></i>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium">
                  {{ analysisResult.success ? 'Verification Successful' : 'Verification Failed' }}
                </h3>
                <div class="mt-2">
                  <p :class="{ 'text-green-700': analysisResult.success, 'text-red-700': !analysisResult.success }">
                    {{ analysisResult.message }}
                  </p>
                </div>

                <!-- Result details -->
                <div v-if="analysisResult.details" class="mt-4 bg-white/50 p-4 rounded-md">
                  <h4 class="font-medium text-sm mb-2">Verification Details</h4>
                  <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                    <div v-for="(value, key) in analysisResult.details" :key="key" class="sm:col-span-1">
                      <dt class="text-xs font-medium text-muted-foreground">{{ key.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') }}</dt>
                      <dd class="mt-1 text-sm text-foreground font-medium">{{ value }}</dd>
                    </div>
                  </dl>
                </div>

                <div class="mt-6 flex space-x-3">
                  <button
                    @click="resetVerification"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                  >
                    <i class="fas fa-redo mr-2"></i>
                    Verify Again
                  </button>

                  <button
                    v-if="analysisResult.success"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                  >
                    <i class="fas fa-download mr-2"></i>
                    Download Certificate
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Next steps -->
          <div v-if="analysisResult.success" class="bg-blue-50 p-4 rounded-lg">
            <h4 class="font-medium text-blue-800 mb-2">What's next?</h4>
            <ul class="space-y-2 text-sm text-blue-700">
              <li class="flex items-start">
                <i class="fas fa-check-circle text-blue-400 mt-0.5 mr-2"></i>
                <span>Your verification has been saved to your account</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-envelope text-blue-400 mt-0.5 mr-2"></i>
                <span>We've sent a confirmation email with your verification details</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-clock text-blue-400 mt-0.5 mr-2"></i>
                <span>Verification is valid for 12 months</span>
              </li>
            </ul>
            <div class="mt-4">
              <div class="text-xs text-muted-foreground">Status</div>
              <div class="font-medium">{{ analysisResult?.policy?.status || 'Pending' }}</div>
            </div>
            <div class="mt-6">
              <div class="text-sm font-medium mb-2">Checks</div>
              <ul v-if="analysisResult?.checks" class="mt-2 grid gap-2 sm:grid-cols-2">
                <li v-for="(check, index) in analysisResult.checks" :key="index" class="flex items-center gap-2 text-sm">
                  <span
                    class="h-2 w-2 rounded-full"
                    :class="check.status === 'passed' || check.status === 'clear' ? 'bg-emerald-500' : 'bg-red-500'"
                  ></span>
                  {{ check.label }} — <span class="text-muted-foreground">{{ check.status }}</span>
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
