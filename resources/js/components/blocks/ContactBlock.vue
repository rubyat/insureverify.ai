<script setup lang="ts">
import { ref, onBeforeUnmount } from 'vue'

const props = defineProps<{ model: Record<string, any> }>()

// Local demo form state (for preview purposes)
const form = ref({ name: '', email: '', phone: '', message: '' })
const submitting = ref(false)
const successLocal = ref<string | null>(null)
let hideTimer: ReturnType<typeof setTimeout> | null = null

function validate(): boolean {
  const emailOk = /.+@.+\..+/.test(String(form.value.email || ''))
  const digits = String(form.value.phone || '').replace(/\D/g, '')
  return (
    String(form.value.name || '').trim().length > 0 &&
    emailOk &&
    digits.length >= 8 &&
    String(form.value.message || '').trim().length >= 10
  )
}

function submit() {
  if (!validate()) return
  submitting.value = true
  setTimeout(() => {
    submitting.value = false
    successLocal.value = props.model.success_text || "Thanks! Your message has been queued. We'll get back within 1–2 business days."
    form.value = { name: '', email: '', phone: '', message: '' }
    if (hideTimer) clearTimeout(hideTimer)
    hideTimer = setTimeout(() => (successLocal.value = null), 5000)
  }, 900)
}

onBeforeUnmount(() => {
  if (hideTimer) clearTimeout(hideTimer)
})
</script>

<template>
  <section class="relative py-16 px-6 md:px-12 max-w-2xl mx-auto">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
      <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-primary/10 to-transparent"></div>
      <div class="absolute left-1/2 top-20 h-56 w-56 -translate-x-1/2 rounded-full bg-primary/10 blur-3xl opacity-30"></div>
    </div>

    <div class="text-center mb-8">
      <h2 class="text-3xl md:text-4xl font-bold">{{ props.model.title || 'Contact Us' }}</h2>
      <p class="text-foreground/70 mt-2">{{ props.model.subtitle || 'Send us a message and we’ll get back within 1–2 business days.' }}</p>
    </div>

    <div class="rounded-xl border bg-background/50 shadow-sm">
      <div v-if="props.model.show_form !== false" class="p-6 md:p-8">
        <form @submit.prevent="submit" class="space-y-6">
          <div v-if="successLocal" class="rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-green-800 text-sm">{{ successLocal }}</div>
          <div class="grid gap-6 md:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-foreground/80">Name</label>
              <input v-model="form.name" type="text" class="mt-1 w-full rounded-lg border bg-background px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-foreground/80">Email</label>
              <input v-model="form.email" type="email" class="mt-1 w-full rounded-lg border bg-background px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition" required />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-foreground/80">Mobile Number</label>
              <input v-model="form.phone" type="tel" inputmode="tel" placeholder="+1 555 123 4567" class="mt-1 w-full rounded-lg border bg-background px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition" required />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-foreground/80">Message</label>
            <textarea v-model="form.message" rows="6" class="mt-1 w-full rounded-lg border bg-background px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition" required />
          </div>

          <div class="flex items-center justify-end gap-4">
            <button type="submit" class="btn-primary px-6 py-2 rounded-md" :disabled="submitting">
              <span>{{ submitting ? 'Sending…' : 'Send Message' }}</span>
            </button>
          </div>
        </form>
      </div>

      <div v-if="props.model.contact_email || props.model.contact_phone || props.model.contact_address" class="px-6 pb-8 text-sm text-foreground/80">
        <div v-if="props.model.contact_email">Email: <a class="text-primary hover:underline" :href="'mailto:' + props.model.contact_email">{{ props.model.contact_email }}</a></div>
        <div v-if="props.model.contact_phone">Phone: <a class="text-primary hover:underline" :href="'tel:' + props.model.contact_phone">{{ props.model.contact_phone }}</a></div>
        <div v-if="props.model.contact_address">Address: <span>{{ props.model.contact_address }}</span></div>
      </div>
    </div>
  </section>
</template>
