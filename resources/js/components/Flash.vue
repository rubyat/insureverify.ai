<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const success = computed(() => (usePage().props.flash as any)?.success)
const error = computed(() => (usePage().props.flash as any)?.error)
</script>

<template>
  <div class="p-4 space-y-2" v-if="success || error">
    <div v-if="success" class="rounded border border-green-200 bg-green-50 text-green-800 px-3 py-2">
      {{ success }}
    </div>
    <div v-if="error" class="rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
      {{ error }}
    </div>
  </div>
</template>
