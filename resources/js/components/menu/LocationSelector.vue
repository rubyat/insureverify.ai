<script setup lang="ts">
import { onMounted, ref } from 'vue'

const props = defineProps<{ modelValue: string }>()
const emit = defineEmits<{ (e: 'update:modelValue', value: string): void }>()

const options = ref<{ key: string; name: string }[]>([])

async function fetchLocations() {
  const res = await fetch(route('admin.menu.locations'), { credentials: 'same-origin' })
  options.value = await res.json()
}

function updateValue(e: Event) {
  const target = e.target as HTMLSelectElement
  emit('update:modelValue', target.value)
}

onMounted(() => {
  fetchLocations()
})
</script>

<template>
  <select class="border rounded px-3 py-2 w-full" :value="props.modelValue || ''" @change="updateValue">
    <option value="">Select a location</option>
    <option v-for="opt in options" :key="opt.key" :value="opt.key">{{ opt.name }}</option>
  </select>

</template>
