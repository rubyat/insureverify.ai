<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3'
import { computed, reactive, ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DynamicSettingField from '@/components/forms/DynamicSettingField.vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{ groups: Record<string, any[]>, codes: string[] }>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Settings', href: '/admin/settings' },
]

const codes = computed(() => props.codes || [])
const activeTab = ref<string>(codes.value[0] || 'general')

// Build editable state
const settings = reactive<Record<string, any[]>>({})
for (const code of codes.value) {
  settings[code] = (props.groups[code] || []).map((s: any) => ({
    id: s.id,
    key: s.key,
    type: s.type,
    label: s.label,
    value: s.value,
    meta: s.meta || null,
  }))
}

const formEl = ref<HTMLFormElement | null>(null)
const page = usePage()
const saving = ref(false)
const success = computed(() => (page.props as any)?.flash?.success ?? '')
const showSuccess = computed(() => !!success.value)

const codeLabel = (code: string) => {
  if (!code) return ''
  const text = code.replace(/_/g, ' ')
  return text.charAt(0).toUpperCase() + text.slice(1)
}

const save = () => {
  if (!formEl.value) return
  const fd = new FormData(formEl.value)
  fd.append('settings', JSON.stringify(settings))
  saving.value = true
  router.post(route('admin.settings.update'), fd, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {},
    onFinish: () => { saving.value = false },
  })
}
</script>

<template>
  <Head title="Settings" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Settings</h1>
        <button @click="save" :disabled="saving" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white disabled:opacity-60 disabled:cursor-not-allowed">
          <svg v-if="saving" class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          <span>{{ saving ? 'Saving…' : 'Save' }}</span>
        </button>
      </div>

      <div v-if="showSuccess" class="rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3">
        {{ success }}
      </div>

      <div class="grid grid-cols-12 gap-6">
        <!-- Sidebar Tabs -->
        <div class="col-span-12 md:col-span-3">
          <div class="rounded border bg-white p-2">
            <button v-for="code in codes" :key="code" class="block w-full text-left px-3 py-2 rounded mb-1"
              :class="[activeTab === code ? 'bg-primary text-white' : 'hover:bg-gray-50']" @click="activeTab = code">
              {{ codeLabel(code) }}
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="col-span-12 md:col-span-9">
          <form ref="formEl" @submit.prevent="save" class="rounded border bg-white p-4 space-y-6">
            <div v-for="field in settings[activeTab]" :key="field.id">
              <DynamicSettingField v-model="field.value" :field="field" />
            </div>
            <div class="pt-2">
              <button type="submit" :disabled="saving" class="rounded bg-primary px-4 py-2 text-white disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center">
                <svg v-if="saving" class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>{{ saving ? 'Saving…' : 'Save changes' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
