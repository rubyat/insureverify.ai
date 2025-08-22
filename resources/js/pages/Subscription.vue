<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
const props = defineProps<{ plan: any; anet: { loginId: string; clientKey: string; env: string } }>();

const form = ref({
  card_number: '',
  expiry_month: '',
  expiry_year: '',
  cvv: '',
  first_name: '',
  last_name: '',
});
const opaque = ref<{ descriptor: string; value: string } | null>(null);
const loading = ref(false);
const message = ref<string | null>(null);

const ensureAcceptJsLoaded = async () => {
  if (typeof window === 'undefined') return;
  // @ts-ignore
  if ((window as any).Accept) return;
  await new Promise<void>((resolve, reject) => {
    const s = document.createElement('script');
    s.src = 'https://js.authorize.net/v1/Accept.js';
    s.async = true;
    s.onload = () => resolve();
    s.onerror = () => reject(new Error('Failed to load Authorize.Net script'));
    document.head.appendChild(s);
  });
};

const tokenize = async () => {
  await ensureAcceptJsLoaded();
  return new Promise<void>((resolve, reject) => {
    const authData = {
      clientKey: props.anet.clientKey,
      apiLoginID: props.anet.loginId,
    };
    const cardData = {
      cardNumber: form.value.card_number,
      month: form.value.expiry_month,
      year: form.value.expiry_year,
      cardCode: form.value.cvv,
    };
    const secureData: any = { authData, cardData };
    // Accept.js injected global function
    // @ts-ignore
    Accept.dispatchData(secureData, (response: any) => {
      if (response.messages.resultCode === 'Error') {
        reject(new Error(response.messages.message[0].text));
        return;
      }
      const opaqueData = response.opaqueData;
      opaque.value = { descriptor: opaqueData.dataDescriptor, value: opaqueData.dataValue };
      resolve();
    });
  });
};

const submit = async () => {
  loading.value = true;
  message.value = null;
  try {
    await tokenize();
  } catch (e: any) {
    message.value = e?.message || 'Tokenization failed';
    loading.value = false;
    return;
  }
  await router.post(
    route('subscription.store'),
    {
      plan_id: props.plan.id,
      opaque_descriptor: opaque.value?.descriptor,
      opaque_value: opaque.value?.value,
      first_name: form.value.first_name,
      last_name: form.value.last_name,
    },
    {
      onError: (errors) => {
        message.value = 'Validation failed';
        loading.value = false;
      },
      onSuccess: () => (loading.value = false),
    },
  );
};
</script>

<template>
  <Head title="Subscribe" />
  <AppLayout>
    <div class="mx-auto max-w-2xl px-6 py-12">
      <h1 class="text-2xl font-semibold">Subscribe to {{ props.plan.name }}</h1>
      <p class="mt-2 text-muted-foreground">Amount: ${{ Number(props.plan.price).toFixed(2) }} / month</p>

      <div class="mt-8 space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium">Card number</label>
            <input v-model="form.card_number" type="text" class="mt-1 w-full rounded border p-2" placeholder="4111111111111111" />
          </div>
          <div>
            <label class="block text-sm font-medium">Expiry month</label>
            <input v-model="form.expiry_month" type="text" class="mt-1 w-full rounded border p-2" placeholder="12" />
          </div>
          <div>
            <label class="block text-sm font-medium">Expiry year</label>
            <input v-model="form.expiry_year" type="text" class="mt-1 w-full rounded border p-2" placeholder="2030" />
          </div>
          <div>
            <label class="block text-sm font-medium">CVV</label>
            <input v-model="form.cvv" type="text" class="mt-1 w-full rounded border p-2" placeholder="123" />
          </div>
          <div>
            <label class="block text-sm font-medium">First name</label>
            <input v-model="form.first_name" type="text" class="mt-1 w-full rounded border p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Last name</label>
            <input v-model="form.last_name" type="text" class="mt-1 w-full rounded border p-2" />
          </div>
        </div>
      </div>

      <div class="mt-6 flex items-center gap-4">
        <button :disabled="loading" @click="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white">
          <span v-if="loading">Processing...</span>
          <span v-else>Confirm and Subscribe</span>
        </button>
        <span v-if="message" class="text-red-600 text-sm">{{ message }}</span>
      </div>
    </div>
  </AppLayout>
</template>


