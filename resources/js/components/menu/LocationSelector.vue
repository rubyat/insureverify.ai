<script setup lang="ts">
import { onMounted, ref } from 'vue'

const props = defineProps<{ modelValue: string[] }>()
const emit = defineEmits<{ (e: 'update:modelValue', value: string[]): void }>()

function toggle(val: string, checked: boolean) {
  const set = new Set(props.modelValue || [])
  if (checked) set.add(val)
  else set.delete(val)
  emit('update:modelValue', Array.from(set))
}

const options = ref<{ key: string; name: string }[]>([])

async function fetchLocations() {
  const res = await fetch(route('admin.menu.locations'), { credentials: 'same-origin' })
  options.value = await res.json()
}

onMounted(() => {
  fetchLocations()
})
</script>

<template>
  <div class="flex gap-4 flex-wrap">
    <label v-for="opt in options" :key="opt.key" class="flex items-center gap-2">
      <input type="checkbox" :checked="props.modelValue?.includes(opt.key)" @change="(e: any) => toggle(opt.key, e.target.checked)" />
      {{ opt.name }}
    </label>
  </div>
</template>
