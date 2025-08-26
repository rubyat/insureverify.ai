<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import CustomerLayout from '@/layouts/customer/Layout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
  currentPlan: { name: string; price: number; renewalDate: string };
  invoices: Array<{ id: string; date: string; amount: number; url: string }>;
  paymentMethod: { brand: string; last4: string; exp: string };
  cards: Array<{ id: number; brand: string; last4: string; exp: string; name: string; is_default: boolean }>;
  errors: string[];
}>();

const page = usePage<any>();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
const breadcrumbItems = [{ title: 'Billing', href: '/app/billing' }];

// Add card modal + form (Signup-style fields)
const showAdd = ref(false);
const addCard = useForm({
  first_name: '',
  last_name: '',
  card: '',
  exp: '',
  cvv: '',
});

// Actions
const makeDefaultForm = useForm({});
const deleteForm = useForm({});

function makeDefault(cardId: number) {
  makeDefaultForm.post(route('app.billing.cards.default', { card: cardId }), {
    preserveScroll: true,
  });
}

function deleteCard(cardId: number) {
  deleteForm.delete(route('app.billing.cards.destroy', { card: cardId }), {
    preserveScroll: true,
  });
}

function openAdd() {
  addCard.reset();
  showAdd.value = true;
}
function closeAdd() {
  showAdd.value = false;
}
</script>

<template>
  <Head title="Billing" />
  <component :is="Layout" :breadcrumbs="breadcrumbItems">
    <CustomerLayout>
      <div class="p-6 space-y-6 max-w-3xl">
      <h1 class="text-2xl font-semibold">Billing</h1>

      <div v-if="errors?.length" class="space-y-2">
        <div v-for="(e, i) in errors" :key="i" class="rounded border border-red-300 bg-red-50 p-3 text-sm text-red-900">{{ e }}</div>
      </div>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Current Plan</h2>
        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-3">
          <div>
            <div class="text-sm text-muted-foreground">Plan</div>
            <div class="font-medium">{{ currentPlan.name }}</div>
          </div>
          <div>
            <div class="text-sm text-muted-foreground">Price</div>
            <div class="font-medium">${{ currentPlan.price.toFixed(2) }}/mo</div>
          </div>
          <div>
            <div class="text-sm text-muted-foreground">Renews</div>
            <div class="font-medium">{{ currentPlan.renewalDate }}</div>
          </div>
        </div>
      </div>

      <div class="rounded border p-4">
        <h2 class="text-lg font-semibold">Payment Method</h2>
        <div class="mt-2 text-sm">{{ paymentMethod.brand }} ending •••• {{ paymentMethod.last4 }} (exp {{ paymentMethod.exp }})</div>

        <div class="mt-4 grid gap-4">
          <div>
            <div class="flex items-center justify-between mb-2">
              <div class="text-sm font-medium">Saved Cards</div>
              <button type="button" class="text-xs rounded border px-3 py-1.5" @click="openAdd">Add Card</button>
            </div>
            <div class="grid gap-3">
              <div
                v-for="c in props.cards || []"
                :key="c.id"
                :class="[
                  'flex items-center justify-between rounded border p-3',
                  c.is_default ? 'border-emerald-400 ring-1 ring-emerald-200 bg-emerald-50/40' : 'bg-white'
                ]"
              >
                <div>
                  <div class="font-medium text-sm">{{ c.brand }} •••• {{ c.last4 }}</div>
                  <div class="text-xs text-muted-foreground">{{ c.name || '—' }} · exp {{ c.exp }}</div>
                </div>
                <div class="flex items-center gap-2">
                  <span v-if="c.is_default" class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">Default</span>
                  <button
                    v-else
                    type="button"
                    class="text-xs rounded border px-2 py-1"
                    :disabled="makeDefaultForm.processing"
                    @click="makeDefault(c.id)"
                  >Make default</button>
                  <button
                    type="button"
                    class="text-xs rounded border px-2 py-1 text-red-700 border-red-300 disabled:opacity-50"
                    :disabled="c.is_default || deleteForm.processing"
                    @click="deleteCard(c.id)"
                  >Delete</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="rounded border">
        <div class="border-b p-3 font-medium">Invoices</div>
        <div class="divide-y">
          <div v-for="inv in invoices" :key="inv.id" class="flex items-center justify-between p-3 text-sm">
            <div>{{ inv.id }}</div>
            <div class="text-muted-foreground">{{ inv.date }}</div>
            <div>${{ inv.amount.toFixed(2) }}</div>
            <a :href="inv.url" class="rounded border px-2 py-1">PDF</a>
          </div>
        </div>
      </div>

      <!-- Add Card Modal -->
      <div v-if="showAdd" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="closeAdd" />
        <div class="relative z-10 w-full max-w-lg rounded-lg bg-white shadow border border-gray-200">
          <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-base font-semibold">Add New Card</h3>
            <button class="text-sm text-gray-500" @click="closeAdd">✕</button>
          </div>
          <form class="p-5 grid gap-3" @submit.prevent="addCard.post(route('app.billing.cards.store'), { preserveScroll: true, onSuccess: () => { closeAdd(); } })">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-700">First Name</label>
                <input v-model="addCard.first_name" type="text" class="mt-1 w-full rounded border px-2 py-1.5 text-sm" />
                <p v-if="addCard.errors.first_name" class="text-xs text-red-600 mt-1">{{ addCard.errors.first_name }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700">Last Name</label>
                <input v-model="addCard.last_name" type="text" class="mt-1 w-full rounded border px-2 py-1.5 text-sm" />
                <p v-if="addCard.errors.last_name" class="text-xs text-red-600 mt-1">{{ addCard.errors.last_name }}</p>
              </div>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700">Credit Card Number</label>
              <input v-model="addCard.card" type="text" inputmode="numeric" placeholder="•••• •••• •••• ••••" class="mt-1 w-full rounded border px-2 py-1.5 text-sm" />
              <p v-if="addCard.errors.card" class="text-xs text-red-600 mt-1">{{ addCard.errors.card }}</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-700">Expiration (MMYY or MM/YYYY)</label>
                <input v-model="addCard.exp" type="text" placeholder="MMYY" class="mt-1 w-full rounded border px-2 py-1.5 text-sm" />
                <p v-if="addCard.errors.exp" class="text-xs text-red-600 mt-1">{{ addCard.errors.exp }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700">CVV</label>
                <input v-model="addCard.cvv" type="text" placeholder="123" class="mt-1 w-full rounded border px-2 py-1.5 text-sm" />
                <p v-if="addCard.errors.cvv" class="text-xs text-red-600 mt-1">{{ addCard.errors.cvv }}</p>
              </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
              <button type="button" class="rounded border px-3 py-1.5 text-sm" @click="closeAdd">Cancel</button>
              <button type="submit" class="btn-primary rounded px-3 py-1.5 text-sm disabled:opacity-60" :disabled="addCard.processing">Save Card</button>
            </div>
            <p v-if="page.props?.flash?.success" class="text-xs text-emerald-700">{{ page.props.flash.success }}</p>
            <p v-if="page.props?.flash?.error" class="text-xs text-red-700">{{ page.props.flash.error }}</p>
          </form>
        </div>
      </div>
      </div>
    </CustomerLayout>
  </component>
</template>
