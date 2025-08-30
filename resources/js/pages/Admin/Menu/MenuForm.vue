<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { watch } from 'vue'
import MenuBuilder from '@/components/menu/MenuBuilder.vue'
import ContentTypePicker from '@/components/menu/ContentTypePicker.vue'
import LocationSelector from '@/components/menu/LocationSelector.vue'

const props = defineProps<{ menu?: any, submitRoute: string, method: 'post' | 'put' }>()

const form = useForm<any>({
  name: props.menu?.name || '',
  status: props.menu?.status || 'active',
  items: props.menu?.items || [],
  location: props.menu?.location || '',
})

watch(() => props.menu, (m) => {
  if (m) {
    form.name = m.name
    form.status = m.status
    form.items = m.items || []
    form.location = m.location || ''
  }
})

const submitRoute = props.submitRoute
const method = props.method
</script>

<template>
  <form @submit.prevent="() => submitRoute && (method === 'post' ? form.post(submitRoute) : form.put(submitRoute))" class="space-y-4">
    <!-- Top: Name field -->
    <input v-model="form.name" type="text" class="border rounded px-3 py-2 w-full" placeholder="Main Menu" />
    <div v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</div>

    <!-- Main two-column layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
      <!-- Left: Accordion content pickers -->
      <div class="lg:col-span-1">
        <div class="sticky top-4">
          <ContentTypePicker @add-items="(items:any[]) => form.items.push(...items)" />
        </div>
      </div>

      <!-- Right: Menu items + configs -->
      <div class="lg:col-span-2 space-y-6">
        <div class="p-4 bg-white border rounded">
          <h3 class="font-medium mb-3">Menu items</h3>
          <MenuBuilder v-model="form.items" />
        </div>

        <div class="p-4 bg-white border rounded">
          <h3 class="font-medium mb-3">Menu Configs</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Status</label>
              <select v-model="form.status" class="border rounded px-3 py-2 w-full">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <div v-if="form.errors.status" class="text-red-600 text-sm mt-1">{{ form.errors.status }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Location</label>
              <LocationSelector v-model="form.location" />
            </div>
          </div>
        </div>

        <!-- Save bar -->
        <div class="flex items-center justify-end">
          <div class="flex items-center gap-2">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">Save Menu</button>
            <Link :href="route('admin.menu.index')" class="px-4 py-2 rounded border">Cancel</Link>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
