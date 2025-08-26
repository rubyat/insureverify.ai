<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { CreditCard, ShieldCheck, Loader2 } from 'lucide-vue-next'

const props = defineProps<{
  plans: Array<any>
  currentPlanId?: number | null
  paymentMethod?: { brand: string; last4: string; exp: string } | null
}>();

const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Upgrade Plan', href: '/app/upgrade' }];

// Inertia form for switching plans
const form = useForm({ plan_id: null as number | null });

// Modal state
const showModal = ref(false)
const selectedPlan = ref<any | null>(null)
const processing = ref(false)

function openModal(plan: any) {
  selectedPlan.value = plan
  showModal.value = true
  processing.value = false
}

const choosePlan = (planId: number) => {
  form.plan_id = planId;
  form.post(route('app.upgrade.switch'), {
    preserveScroll: true,
    onSuccess: () => {
      showModal.value = false;
    },
    onFinish: () => {
      processing.value = false;
      showModal.value = false;
    },
  });
};

async function confirmUpgrade() {
  if (!selectedPlan.value) return
  processing.value = true
  // Fake processing delay + security reassurance
  await new Promise((r) => setTimeout(r, 1400))
  choosePlan(selectedPlan.value.id)
}
</script>

<template>
  <Head title="Upgrade Plan" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6">
        <h1 class="text-2xl font-semibold">Choose a plan</h1>
        <p class="text-muted-foreground mt-1">Select the plan that best fits your usage. Your current plan is highlighted.</p>

        <section class="bg-gray-100 mt-6 rounded-xl py-10 px-6 md:px-10">
          <div class="container mx-auto">
            <div class="text-center mb-10">
              <h2 class="text-3xl md:text-4xl font-bold">Pricing</h2>
              <p class="text-foreground/70 mt-2">Simple, transparent pricing that scales with your business</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-6 mx-auto">
              <div
                v-for="plan in props.plans ?? []"
                :key="plan.id"
                :class="[
                  'border rounded-lg p-6 flex flex-col bg-white transition-all duration-300 ease-out transform hover:-translate-y-1 hover:scale-[1.03] hover:shadow-xl hover:shadow-sky-900/10',
                  plan.id === props.currentPlanId ? 'border-sky-500 ring-2 ring-sky-200' : 'hover:border-sky-200/70'
                ]"
              >
                <div class="flex items-start justify-between">
                  <h3 class="font-semibold text-lg">{{ plan.name }}</h3>
                  <span v-if="plan.id === props.currentPlanId" class="text-xs rounded-full bg-sky-100 text-sky-700 px-2 py-1">Current</span>
                </div>

                <div class="mt-2 text-3xl font-extrabold tracking-tight">
                  <template v-if="plan.price !== null && plan.price !== undefined">
                    ${{ Number(plan.price).toFixed(0) }} <span class="text-base font-medium text-foreground/70">/month</span>
                  </template>
                  <template v-else>
                    Custom Pricing
                  </template>
                </div>

                <p v-if="plan.verifications_included" class="text-sm text-foreground/70 mt-2">{{ plan.verifications_included }} verifications included</p>
                <p v-if="plan.description" class="text-sm mt-4 text-foreground/80">{{ plan.description }}</p>

                <ul class="mt-6 space-y-3 text-sm text-foreground/80">
                  <li v-for="(feat, idx) in (plan.features || [])" :key="idx" class="flex items-start gap-2">
                    <span class="mt-1 w-2 h-2 bg-black rounded-full"></span>
                    <span>{{ feat }}</span>
                  </li>
                </ul>

                <div class="mt-auto pt-8">
                  <button
                    v-if="plan.id === props.currentPlanId"
                    class="w-full px-4 py-3 rounded border bg-gray-100 text-gray-600 cursor-default"
                    disabled
                    aria-disabled="true"
                  >
                    Current Plan
                  </button>
                  <button
                    v-else
                    type="button"
                    class="btn-primary w-full text-center px-4 py-3 rounded disabled:opacity-60"
                    :disabled="form.processing"
                    @click="openModal(plan)"
                  >
                    <span v-if="form.processing">Switching…</span>
                    <span v-else>{{ plan.cta_label || 'Choose Plan' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Upgrade Confirmation Modal -->
        <transition name="fade">
          <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="showModal = false"></div>
            <div class="relative z-10 w-full max-w-lg rounded-xl border bg-white shadow-xl">
              <div class="p-6 md:p-7 space-y-6">
                <div class="flex items-start gap-3">
                  <div class="rounded-lg bg-primary/10 text-primary p-2 ring-1 ring-primary/20">
                    <CreditCard class="w-5 h-5" />
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold">Confirm upgrade</h3>
                    <p class="text-sm text-foreground/70">Secure payment powered by PCI-compliant provider.</p>
                  </div>
                </div>

                <div class="rounded-lg border p-4 bg-gray-50">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-sm text-foreground/70">Plan</div>
                      <div class="font-medium">{{ selectedPlan?.name }}</div>
                    </div>
                    <div class="text-right">
                      <div class="text-sm text-foreground/70">Price</div>
                      <div class="font-semibold">{{ selectedPlan?.price != null ? `$${Number(selectedPlan?.price).toFixed(2)}/mo` : 'Custom' }}</div>
                    </div>
                  </div>
                </div>

                <div class="space-y-4">
                  <div class="rounded-lg border p-4 bg-white">
                    <div class="flex items-center justify-between">
                      <div>
                        <div class="text-sm text-foreground/70">Payment Method</div>
                        <div v-if="props.paymentMethod" class="font-medium">
                          {{ props.paymentMethod.brand }} ending •••• {{ props.paymentMethod.last4 }} (exp {{ props.paymentMethod.exp }})
                        </div>
                        <div v-else class="text-sm text-foreground/70">
                          No card on file. Please add one on the Billing page.
                        </div>
                      </div>
                      <a href="/app/billing" class="text-sm underline">Manage cards</a>
                    </div>
                  </div>
                </div>

                <div class="flex items-center justify-between text-xs text-foreground/70">
                  <div class="inline-flex items-center gap-2">
                    <ShieldCheck class="w-4 h-4 text-emerald-600" />
                    <span>Your payment info is encrypted & never stored on our servers.</span>
                  </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                  <button type="button" class="px-4 py-2 rounded border" :disabled="processing || form.processing" @click="showModal = false">Cancel</button>
                  <button type="button" class="btn-primary inline-flex items-center gap-2 px-5 py-2 rounded disabled:opacity-60" :disabled="processing || form.processing" @click="confirmUpgrade">
                    <Loader2 v-if="processing || form.processing" class="h-4 w-4 animate-spin" />
                    <span>{{ (processing || form.processing) ? 'Processing…' : 'Confirm & Upgrade' }}</span>
                  </button>
                </div>

                <!-- Processing overlay -->
                <transition name="fade">
                  <div v-if="processing || form.processing" class="absolute inset-0 rounded-xl bg-white/70 backdrop-blur-sm flex items-center justify-center">
                    <div class="flex flex-col items-center gap-3">
                      <Loader2 class="h-8 w-8 animate-spin text-primary" />
                      <div class="text-sm text-foreground/70">Securing payment and upgrading plan…</div>
                    </div>
                  </div>
                </transition>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </CustomerLayout>
  </component>
</template>
