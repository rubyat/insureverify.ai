<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import InputError from '@/components/InputError.vue'
import { Head, useForm } from '@inertiajs/vue3'

defineProps<{
  status?: string
  canResetPassword: boolean
}>()

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Log in" />
  <SiteLayout>
    <section class="py-12 px-4">
      <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-xl shadow border border-gray-200">
          <div class="px-6 sm:px-8 pt-8">
            <h1 class="text-center text-2xl sm:text-3xl font-bold text-gray-900">Log in to your account</h1>
            <p class="mt-2 text-center text-sm text-gray-600">Enter your email and password below to continue.</p>
            <p class="mt-2 text-center text-sm text-gray-500">Don't have an account? <a :href="route('signup')" class="text-sky-600 hover:underline">Sign up</a></p>
          </div>

          <form @submit.prevent="submit" class="px-6 sm:px-8 pb-8 mt-6">
            <div class="grid grid-cols-1 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700" for="email">Email address</label>
                <input
                  id="email"
                  type="email"
                  required
                  autofocus
                  autocomplete="email"
                  v-model="form.email"
                  placeholder="email@example.com"
                  class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                />
                <InputError :message="form.errors.email" />
              </div>

              <div>
                <div class="flex items-center justify-between">
                  <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
                  <a v-if="canResetPassword" :href="route('password.request')" class="text-sm text-sky-600 hover:underline">Forgot password?</a>
                </div>
                <input
                  id="password"
                  type="password"
                  required
                  autocomplete="current-password"
                  v-model="form.password"
                  placeholder="Password"
                  class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                />
                <InputError :message="form.errors.password" />
              </div>

              <label class="mt-1 flex items-center gap-3 text-sm text-gray-700">
                <input id="remember" type="checkbox" v-model="form.remember" class="rounded" />
                <span>Remember me</span>
              </label>

              <button type="submit" :disabled="form.processing" class="mt-2 w-full btn-primary px-6 py-3 rounded-md disabled:opacity-70">Log in</button>

              <div class="my-2 flex items-center gap-4 text-gray-400">
                <div class="h-px flex-1 bg-gray-200" />
                <span class="text-xs">or</span>
                <div class="h-px flex-1 bg-gray-200" />
              </div>

              <button type="button" class="w-full px-6 py-3 rounded-md bg-white border border-gray-300 hover:bg-gray-50 flex items-center justify-center gap-2">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" class="h-5 w-5" />
                <span>Continue with Google</span>
              </button>

              <div v-if="status" class="text-center text-sm font-medium text-green-600 mt-2">{{ status }}</div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
