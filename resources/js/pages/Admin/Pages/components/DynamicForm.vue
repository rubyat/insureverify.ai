<script setup lang="ts">
import { ref, watch, toRefs, nextTick } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import ImagePicker from '@/components/filemanager/ImagePicker.vue'

const props = defineProps<{ node: any; block: any; thumbEndpoint?: string }>()
const { node, block } = toRefs(props)

const emit = defineEmits<{ (e: 'update:node', payload: any): void }>()

const model = ref<any>({ ...(node.value?.model || {}) })
// Prevent feedback loop when parent replaces node and we mirror it into local model
let syncingFromParent = false
watch(model, (v) => {
  if (syncingFromParent) return
  if (node.value) emit('update:node', { ...node.value, model: { ...v } })
}, { deep: true })

watch(node, (n) => {
  syncingFromParent = true
  model.value = { ...(n?.model || {}) }
  nextTick(() => { syncingFromParent = false })
})

function initField(s: any) {
  const id = s.id
  if (model.value[id] === undefined) {
    model.value[id] = s.std ?? (s.type === 'listItem' ? [] : '')
  }
}

// Initialize fields whenever block.settings changes (and on mount)
watch(block, (b) => {
  ;(b?.settings || []).forEach((s: any) => initField(s))
}, { immediate: true, deep: true })


</script>

<template>
  <div class="space-y-3" v-if="block">
    <template v-for="s in (block.settings || [])" :key="s.id">
      <div>
        <label class="block text-sm">{{ s.label || s.id }}</label>

        <template v-if="s.type === 'input'">
          <input v-model="model[s.id]" :type="s.inputType || 'text'" class="mt-1 w-full rounded border px-3 py-2" />
        </template>

        <template v-else-if="s.type === 'editor'">
          <QuillEditor v-model:content="model[s.id]" contentType="html" theme="snow" class="mt-1 bg-white" />
        </template>

        <template v-else-if="s.type === 'uploader'">
            <ImagePicker
            v-model="model[s.id]"
            :placeholder="model['placeholder'] || s.placeholder || '/storage/placeholder.png'"
          />
        </template>

        <template v-else-if="s.type === 'radios'">
          <div class="mt-1 flex flex-wrap gap-2">
            <label v-for="v in (s.values || [])" :key="v" class="inline-flex items-center gap-1 text-sm">
              <input type="radio" :name="`r_${s.id}`" :value="v" v-model="model[s.id]" /> {{ v }}
            </label>
          </div>
        </template>

        <template v-else-if="s.type === 'listItem'">
          <div class="mt-2 space-y-3">
            <div
              v-for="(it, idx) in (model[s.id] || [])"
              :key="idx"
              class="rounded border p-3 space-y-2"
            >
              <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">Item {{ idx + 1 }}</div>
                <button type="button" class="text-red-600 text-xs" @click="(model[s.id] = (model[s.id] || []).filter((_: any, i: number) => i !== idx))">Remove</button>
              </div>

              <template v-for="cs in (s.settings || [])" :key="cs.id">
                <div>
                  <label class="block text-xs">{{ cs.label || cs.id }}</label>
                  <template v-if="cs.type === 'input'">
                    <input v-model="it[cs.id]" :type="cs.inputType || 'text'" class="mt-1 w-full rounded border px-3 py-2" />
                  </template>
                  <template v-else-if="cs.type === 'editor'">
                    <QuillEditor v-model:content="it[cs.id]" contentType="html" theme="snow" class="mt-1 bg-white" />
                  </template>
                  <template v-else>
                    <input v-model="it[cs.id]" type="text" class="mt-1 w-full rounded border px-3 py-2" />
                  </template>
                </div>
              </template>
            </div>

            <div>
              <button
                type="button"
                class="rounded border px-3 py-1 text-sm"
                @click="(() => { const arr = (model[s.id] = (model[s.id] || [])); const defaults: any = {}; (s.settings || []).forEach((cs: any) => { defaults[cs.id] = cs.std ?? ''; }); arr.push(defaults) })()"
              >
                + Add Item
              </button>
            </div>
          </div>
        </template>

        <template v-else>
          <input v-model="model[s.id]" type="text" class="mt-1 w-full rounded border px-3 py-2" />
        </template>
      </div>
    </template>
  </div>
</template>
