<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import ImagePicker from '@/components/filemanager/ImagePicker.vue'

const props = defineProps<{
  errors?: Record<string, any>
  processing?: boolean
  onSubmit: () => void
  submitLabel?: string
}>()

// Per-field v-models to work seamlessly with Inertia useForm
const name = defineModel<string>('name')
const slug = defineModel<string>('slug')
const stripe_plan_id = defineModel<string>('stripe_plan_id')
const anet_plan_id = defineModel<string>('anet_plan_id')
const price = defineModel<string | number>('price')
const image = defineModel<string>('image')
const image_limit = defineModel<number>('image_limit')
const description = defineModel<string>('description')
const verifications_included = defineModel<string | number>('verifications_included')
const features = defineModel<string>('features')
const cta_label = defineModel<string>('cta_label')
const cta_route = defineModel<string>('cta_route')
const sort_order = defineModel<number>('sort_order')
const visibility = defineModel<string>('visibility')
const is_active = defineModel<boolean>('is_active')

const submitText = computed(() => props.submitLabel ?? 'Save')

// Slug auto-generation logic
const slugManuallyEdited = ref(false)
const slugify = (val: string) =>
  (val || '')
    .toString()
    .normalize('NFKD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .replace(/-{2,}/g, '-')

watch(name, (val) => {
  if (!slugManuallyEdited.value) {
    slug.value = slugify((val as unknown as string) || '')
  }
})
</script>

<template>
  <div class="rounded-lg border bg-white p-6 shadow-sm">
    <form @submit.prevent="props.onSubmit" class="space-y-6">
      <!-- Row: Name & Slug -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">Name</label>
          <input v-model="name" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.name" class="text-sm text-red-600">{{ props.errors.name }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Slug</label>
          <input v-model="slug" readonly @input="slugManuallyEdited = true" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.slug" class="text-sm text-red-600">{{ props.errors.slug }}</div>
        </div>
      </div>

      <!-- Visibility -->
      <div>
        <label class="block text-sm font-medium">Visibility</label>
        <select v-model="visibility" class="mt-1 w-full rounded border px-3 py-2">
          <option value="Public">Public</option>
          <option value="Private">Private</option>
        </select>
        <div v-if="props.errors?.visibility" class="text-sm text-red-600">{{ props.errors.visibility }}</div>
      </div>

      <!-- Row: Stripe & Authorize.Net IDs -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">Stripe Plan ID</label>
          <input v-model="stripe_plan_id" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.stripe_plan_id" class="text-sm text-red-600">{{ props.errors.stripe_plan_id }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Authorize.Net Plan ID</label>
          <input v-model="anet_plan_id" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.anet_plan_id" class="text-sm text-red-600">{{ props.errors.anet_plan_id }}</div>
        </div>
      </div>

      <!-- Row: Price & Image Limit -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">Price</label>
          <input v-model="price" type="number" step="0.01" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.price" class="text-sm text-red-600">{{ props.errors.price }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Image</label>
          <div class="mt-1">
            <ImagePicker name="image" v-model="image" :baseUrl="'/storage'" :placeholder="''" />
          </div>
          <div v-if="props.errors?.image" class="text-sm text-red-600">{{ props.errors.image }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Image Limit</label>
          <input v-model.number="image_limit" type="number" min="0" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.image_limit" class="text-sm text-red-600">{{ props.errors.image_limit }}</div>
        </div>
      </div>

      <!-- Full width: Verifications Included -->
      <div>
        <label class="block text-sm font-medium">Verifications Included</label>
        <input v-model.number="verifications_included" type="number" min="0" class="mt-1 w-full rounded border px-3 py-2" />
        <div v-if="props.errors?.verifications_included" class="text-sm text-red-600">{{ props.errors.verifications_included }}</div>
      </div>

      <!-- Full width: Description -->
      <div>
        <label class="block text-sm font-medium">Description</label>
        <textarea v-model="description" rows="3" class="mt-1 w-full rounded border px-3 py-2"></textarea>
        <div v-if="props.errors?.description" class="text-sm text-red-600">{{ props.errors.description }}</div>
      </div>

      <!-- Full width: Features -->
      <div>
        <label class="block text-sm font-medium">Features (one per line)</label>
        <textarea v-model="features" rows="6" class="mt-1 w-full rounded border px-3 py-2"></textarea>
        <div v-if="props.errors?.features" class="text-sm text-red-600">{{ props.errors.features }}</div>
      </div>

      <!-- Row: CTA Label & Route -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">CTA Label</label>
          <input v-model="cta_label" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.cta_label" class="text-sm text-red-600">{{ props.errors.cta_label }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">CTA Route (Laravel route name)</label>
          <input v-model="cta_route" type="text" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.cta_route" class="text-sm text-red-600">{{ props.errors.cta_route }}</div>
        </div>
      </div>

      <!-- Row: Sort Order & Active -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">Sort Order</label>
          <input v-model.number="sort_order" type="number" class="mt-1 w-full rounded border px-3 py-2" />
          <div v-if="props.errors?.sort_order" class="text-sm text-red-600">{{ props.errors.sort_order }}</div>
        </div>
        <div class="flex items-center gap-2 md:mt-6">
          <input id="is_active" v-model="is_active" type="checkbox" class="h-4 w-4" />
          <label for="is_active" class="text-sm">Active</label>
        </div>
      </div>

      <div>
        <button :disabled="props.processing" type="submit" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white cursor-pointer">{{ submitText }}</button>
      </div>
    </form>
  </div>
</template>
