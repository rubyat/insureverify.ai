<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
  plan: any
  plans?: Array<{
    id: number
    name: string
    slug: string
    price?: number | null
    verifications_included?: number | string | null
  }>
}>()

const formattedPrice = (price: any) => {
  if (price === null || price === undefined) return 'Custom Pricing'
  try {
    return `$${Number(price).toFixed(0)} /month`
  } catch {
    return `$${price} /month`
  }
}

// Signup form state
const form = useForm({
  plan: (props.plan?.slug as string) || '',
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
  // Optional payment placeholders
  card: '',
  exp: '',
  cvv: '',
})

// React to plan selection to drive the summary card
const selectedPlan = computed(() => {
  return (props.plans || []).find((p) => p.slug === form.plan) || props.plan || {}
})

const submit = () => {
  form.post(route('signup.store'))
}
</script>

<template>
  <Head :title="`${selectedPlan?.name ?? 'Plan'} - Signup`" />
  <SiteLayout>
    <section class="bg-gray-100 py-16 px-6 md:px-12">
      <div class="container mx-auto max-w-3xl">
        <!-- Plan Summary Card (above the form) -->
        <div class="bg-white rounded-xl shadow border p-4 md:p-5 mb-6">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-baseline gap-3">
              <h1 class="text-2xl md:text-3xl font-extrabold">{{ selectedPlan?.name }}</h1>
              <span class="text-base md:text-lg font-semibold text-foreground/80">{{ formattedPrice(selectedPlan?.price) }}</span>
            </div>
            <span v-if="selectedPlan?.verifications_included" class="inline-flex items-center gap-2 text-[11px] px-2.5 py-1 rounded-full bg-gray-100 border text-foreground/80">
              <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              {{ selectedPlan?.verifications_included }} verifications included
            </span>
          </div>

          <div class="mt-3">
            <p class="text-xs text-foreground/60">Selected plan: <code class="bg-gray-100 px-1 py-0.5 rounded">{{ form.plan }}</code></p>
          </div>
        </div>

        <!-- Signup Form Card (mirrors pages/Signup.vue) -->
        <div class="bg-white rounded-xl shadow border border-gray-200">
          <div class="px-6 sm:px-8 pt-8">
            <h1 class="text-center text-2xl sm:text-3xl font-bold text-gray-900">Sign Up for API Access</h1>
            <p class="mt-2 text-center text-sm text-gray-600">Create an account to request an API key for driving license and insurance verification.</p>
            <p class="mt-2 text-center text-sm text-gray-500">Already have an account? <a :href="route('login')" class="text-sky-600 hover:underline">Login</a></p>
          </div>

          <form class="px-6 sm:px-8 pb-8 mt-6" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Choose a Plan <span class="text-red-500">*</span></label>
                <select v-model="form.plan" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white">
                  <option value="">-- Select a Plan --</option>
                  <option v-for="p in (props.plans || [])" :key="p.slug" :value="p.slug">{{ p.name }}</option>
                </select>
                <div v-if="form.errors.plan" class="mt-1 text-sm text-red-600">{{ form.errors.plan }}</div>
              </div>

              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                  <input v-model="form.first_name" type="text" placeholder="John" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
                  <div v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">{{ form.errors.first_name }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                  <input v-model="form.last_name" type="text" placeholder="Doe" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
                  <div v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">{{ form.errors.last_name }}</div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Business Email <span class="text-red-500">*</span></label>
                <input v-model="form.email" type="email" placeholder="name@company.com" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
              </div>

              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Password: <span class="text-red-500">*</span></label>
                  <input v-model="form.password" type="password" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
                  <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Confirm Password: <span class="text-red-500">*</span></label>
                  <input v-model="form.password_confirmation" type="password" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
                  <div v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ form.errors.password_confirmation }}</div>
                </div>
              </div>

              <div class="mt-4">
                <h2 class="text-gray-900 font-semibold">Payment Information</h2>
                <div class="mt-3 space-y-4 rounded-lg border border-gray-200 p-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Credit Card Number <span class="text-red-500">*</span></label>
                    <input v-model="form.card" type="text" inputmode="numeric" placeholder="•••• •••• •••• ••••" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
                    <div v-if="form.errors.card" class="mt-1 text-sm text-red-600">{{ form.errors.card }}</div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Expiration Date (MM/YY) <span class="text-red-500">*</span></label>
                      <input v-model="form.exp" type="text" placeholder="MMYY" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
                      <div v-if="form.errors.exp" class="mt-1 text-sm text-red-600">{{ form.errors.exp }}</div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">CVV <span class="text-red-500">*</span></label>
                      <input v-model="form.cvv" type="text" placeholder="123" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
                      <div v-if="form.errors.cvv" class="mt-1 text-sm text-red-600">{{ form.errors.cvv }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <label class="mt-2 flex items-start gap-3 text-sm text-gray-700">
                <input type="checkbox" v-model="form.terms" class="mt-1" />
                <span>I agree to the <a :href="route('terms')" class="text-sky-600 hover:underline">terms and conditions</a> of API usage.</span>
              </label>
              <div v-if="form.errors.terms" class="mt-1 text-sm text-red-600">{{ form.errors.terms }}</div>

              <button type="submit" class="mt-2 w-full btn-primary px-6 py-3 rounded-md" :disabled="form.processing">
                <span v-if="form.processing">Creating...</span>
                <span v-else>Create Account</span>
              </button>

              <div class="my-2 flex items-center gap-4 text-gray-400">
                <div class="h-px flex-1 bg-gray-200" />
                <span class="text-xs">or</span>
                <div class="h-px flex-1 bg-gray-200" />
              </div>

              <button type="button" class="w-full px-6 py-3 rounded-md bg-white border border-gray-300 hover:bg-gray-50 flex items-center justify-center gap-2">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" class="h-5 w-5" />
                <span>Sign up with Google</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
