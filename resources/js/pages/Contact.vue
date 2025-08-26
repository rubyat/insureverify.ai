<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref, onBeforeUnmount } from 'vue'
import { MessageSquare, Loader2 } from 'lucide-vue-next'

const form = useForm({ name: '', email: '', phone: '', message: '' })

// Local UX state
const submitting = ref(false)
const successLocal = ref<string | null>(null)
let hideTimer: ReturnType<typeof setTimeout> | null = null

function validate() {
  form.clearErrors()
  const errors: Record<string, string> = {}
  if (!form.name?.trim()) errors.name = 'Please enter your name.'
  const email = String(form.email || '')
  const emailOk = /.+@.+\..+/.test(email)
  if (!emailOk) errors.email = 'Please enter a valid email address.'
  // Basic phone validation: allow symbols, check digits length
  const digits = String(form.phone || '').replace(/\D/g, '')
  if (digits.length < 8) errors.phone = 'Please enter a valid mobile number.'
  if (!form.message?.trim() || form.message.length < 10) errors.message = 'Message should be at least 10 characters.'
  if (Object.keys(errors).length) {
    form.setError(errors)
    return false
  }
  return true
}

function submit() {
  successLocal.value = null
  if (!validate()) return

  submitting.value = true
  // Fake send delay
  setTimeout(() => {
    submitting.value = false
    successLocal.value = 'Thanks! Your message has been queued. We\'ll get back within 1–2 business days.'
    form.reset()
    form.clearErrors()
    if (hideTimer) clearTimeout(hideTimer)
    hideTimer = setTimeout(() => (successLocal.value = null), 5000)
  }, 1400)
}

// Flash message from server (kept for future real submission)
const page = usePage<any>()
const flashSuccess = computed(() => page.props?.flash?.success as string | undefined)

onBeforeUnmount(() => {
  if (hideTimer) clearTimeout(hideTimer)
})
</script>

<template>
  <Head title="Contact" />
  <SiteLayout>
    <section class="relative py-16 px-6 md:px-12 max-w-2xl mx-auto">
      <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-primary/10 to-transparent"></div>
        <div class="absolute left-1/2 top-20 h-56 w-56 -translate-x-1/2 rounded-full bg-primary/10 blur-3xl opacity-30"></div>
      </div>
      <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 text-primary mb-2">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10 ring-1 ring-primary/20">
            <MessageSquare class="h-5 w-5" />
          </span>
          <span class="font-medium">We'd love to hear from you</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold">Contact Us</h1>
        <p class="text-foreground/70 mt-2">Send us a message and we’ll get back within 1–2 business days.</p>
      </div>

      <div class="rounded-xl border bg-background/50 shadow-sm">
        <form @submit.prevent="submit" class="p-6 md:p-8 space-y-6">
          <div v-if="successLocal || flashSuccess" class="rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-green-800 text-sm">
            {{ successLocal || flashSuccess }}
          </div>
          <div class="grid gap-6 md:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-foreground/80">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="mt-1 w-full rounded-lg border bg-background px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition"
                required
              />
              <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-foreground/80">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="mt-1 w-full rounded-lg border bg-background px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition"
                required
              />
              <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-foreground/80">Mobile Number</label>
              <input
                v-model="form.phone"
                type="tel"
                inputmode="tel"
                placeholder="+1 555 123 4567"
                class="mt-1 w-full rounded-lg border bg-background px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition"
                required
              />
              <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}</div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-foreground/80">Message</label>
            <textarea
              v-model="form.message"
              rows="6"
              class="mt-1 w-full rounded-lg border bg-background px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition"
              required
            />
            <div v-if="form.errors.message" class="text-red-600 text-sm mt-1">{{ form.errors.message }}</div>
          </div>

          <div class="flex items-center justify-between gap-4">
            <div class="h-5">
              <p v-if="flashSuccess && !successLocal" class="text-green-700 text-sm">{{ flashSuccess }}</p>
            </div>
            <button type="submit" class="btn-primary px-6 py-2 rounded-md inline-flex items-center gap-2" :disabled="submitting">
              <Loader2 v-if="submitting" class="h-4 w-4 animate-spin" />
              <span>{{ submitting ? 'Sending…' : 'Send Message' }}</span>
            </button>
          </div>
        </form>
      </div>
    </section>
  </SiteLayout>
</template>
