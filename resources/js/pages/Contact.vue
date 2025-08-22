<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const form = useForm({ name: '', email: '', message: '' })
function submit() {
  form.post(route('contact.submit'))
}

// Flash message (typed loosely to avoid template TS errors)
const page = usePage<any>()
const flashSuccess = computed(() => page.props?.flash?.success as string | undefined)
</script>

<template>
  <Head title="Contact" />
  <SiteLayout>
    <section class="py-16 px-6 md:px-12 max-w-xl mx-auto">
      <h1 class="text-3xl md:text-4xl font-bold mb-6">Contact Us</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Name</label>
          <input v-model="form.name" type="text" class="mt-1 w-full border rounded px-3 py-2" required />
          <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Email</label>
          <input v-model="form.email" type="email" class="mt-1 w-full border rounded px-3 py-2" required />
          <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Message</label>
          <textarea v-model="form.message" rows="5" class="mt-1 w-full border rounded px-3 py-2" required />
          <div v-if="form.errors.message" class="text-red-600 text-sm mt-1">{{ form.errors.message }}</div>
        </div>
        <button type="submit" class="btn-primary px-6 py-2 rounded" :disabled="form.processing">Send Message</button>
        <p v-if="flashSuccess" class="text-green-700 mt-2">{{ flashSuccess }}</p>
      </form>
    </section>
  </SiteLayout>
</template>
