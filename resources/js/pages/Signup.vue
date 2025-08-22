<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head } from '@inertiajs/vue3'
import { reactive } from 'vue'

const form = reactive({ plan: '', name: '', email: '', password: '', password_confirmation: '', card: '', exp: '', cvv: '', terms: false })
</script>

<template>
  <Head title="Sign Up" />
  <SiteLayout>
    <section class="py-16 px-6 md:px-12 max-w-2xl mx-auto">
      <h1 class="text-3xl md:text-4xl font-bold mb-6">Sign Up for API Access</h1>
      <form class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-sm font-medium">Choose a Plan</label>
          <select v-model="form.plan" class="mt-1 w-full border rounded px-3 py-2">
            <option value="">Select plan</option>
            <option>Silver</option>
            <option>Bronze</option>
            <option>Gold</option>
            <option>Platinum</option>
            <option>Enterprise</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Full Name</label>
          <input v-model="form.name" type="text" class="mt-1 w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium">Business Email</label>
          <input v-model="form.email" type="email" class="mt-1 w-full border rounded px-3 py-2" />
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Password</label>
            <input v-model="form.password" type="password" class="mt-1 w-full border rounded px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" class="mt-1 w-full border rounded px-3 py-2" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium">Credit Card</label>
          <input v-model="form.card" type="text" placeholder="0000 0000 0000 0000" class="mt-1 w-full border rounded px-3 py-2" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Expiration</label>
            <input v-model="form.exp" type="text" placeholder="MM/YY" class="mt-1 w-full border rounded px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">CVV</label>
            <input v-model="form.cvv" type="text" class="mt-1 w-full border rounded px-3 py-2" />
          </div>
        </div>
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" v-model="form.terms" />
          <span>I accept the <a :href="route('terms')" class="underline">terms & conditions</a>.</span>
        </label>
        <button type="button" class="btn-primary px-6 py-3 rounded">Create Account</button>
        <button type="button" class="px-6 py-3 rounded bg-white border">Sign up with Google</button>
      </form>
    </section>
  </SiteLayout>
</template>
