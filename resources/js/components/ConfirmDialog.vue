<!--
Reusable confirmation dialog component.

Usage example:

<ConfirmDialog
  title="Are you sure?"
  confirm-text="Delete"
  cancel-text="Cancel"
  @confirm="handleDelete()"
>
  <template #trigger>
    <button class="rounded border border-red-200 bg-red-50 px-3 py-1.5 text-sm text-red-700 hover:bg-red-100">Delete</button>
  </template>
</ConfirmDialog>

Place any clickable element in the `#trigger` slot. On confirm, the component emits `confirm`.
It also emits `cancel` and supports v-model:open for controlled usage.
-->
<script setup lang="ts">
import { ref, computed } from 'vue'
import { Button } from '@/components/ui/button'
import Dialog from '@/components/ui/dialog/Dialog.vue'
import DialogClose from '@/components/ui/dialog/DialogClose.vue'
import DialogContent from '@/components/ui/dialog/DialogContent.vue'
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue'
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue'
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue'
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue'

const props = withDefaults(defineProps<{
  title?: string
  description?: string
  confirmText?: string
  cancelText?: string
  // Optional controlled open
  open?: boolean
}>(), {
  title: 'Are you sure?',
  description: '',
  confirmText: 'Delete',
  cancelText: 'Cancel',
})

const emit = defineEmits<{
  (e: 'confirm'): void
  (e: 'cancel'): void
  (e: 'update:open', value: boolean): void
}>()

const internalOpen = ref(false)
const isOpen = computed({
  get: () => props.open ?? internalOpen.value,
  set: (v: boolean) => {
    if (props.open === undefined) internalOpen.value = v
    emit('update:open', v)
  },
})

const onConfirm = () => {
  emit('confirm')
  isOpen.value = false
}
const onCancel = () => {
  emit('cancel')
  isOpen.value = false
}
</script>

<template>
  <span>
    <!-- Trigger rendered outside the Dialog so it is always visible -->
    <span @click="isOpen = true">
      <slot name="trigger" />
    </span>
    <Dialog v-model:open="isOpen">
      <DialogContent>
        <DialogHeader class="space-y-3">
          <DialogTitle>{{ props.title }}</DialogTitle>
          <DialogDescription v-if="props.description">
            {{ props.description }}
          </DialogDescription>
        </DialogHeader>

        <DialogFooter class="gap-2">
          <DialogClose as-child>
            <Button variant="secondary" @click="onCancel">{{ props.cancelText }}</Button>
          </DialogClose>
          <Button variant="destructive" @click="onConfirm">{{ props.confirmText }}</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </span>
</template>
