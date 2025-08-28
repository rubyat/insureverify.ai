<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{ payment: any }>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/admin/dashboard' },
  { title: 'Payments', href: '/admin/payments' },
  { title: `Payment #${props.payment?.id ?? ''}`, href: '#' },
]
</script>

<template>
  <Head :title="`Payment #${payment?.id || ''}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6 bg-gray-50">
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-xl font-semibold">Payment #{{ payment.id }}</h1>
          <div class="text-gray-600 text-sm">Status: {{ payment.status }} · Provider: {{ payment.provider }}</div>
        </div>
        <Link :href="route('admin.payments.index')" class="rounded border px-3 py-1.5">Back</Link>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="rounded border bg-white p-4">
          <h2 class="mb-3 text-base font-semibold">Details</h2>
          <div class="space-y-1 text-sm">
            <div><span class="text-gray-500">Amount:</span> ${{ payment.amount.toFixed(2) }} {{ payment.currency }}</div>
            <div><span class="text-gray-500">Paid at:</span> {{ payment.paid_at || '-' }}</div>
            <div><span class="text-gray-500">Invoice:</span> {{ payment.invoice?.number }} ({{ payment.invoice?.status }})</div>
            <div v-if="payment.user"><span class="text-gray-500">User:</span> {{ payment.user.name }} ({{ payment.user.email }})</div>
          </div>
        </div>
        <div class="rounded border bg-white p-4" v-if="payment.error_code || payment.error_message">
          <h2 class="mb-3 text-base font-semibold text-red-600">Error</h2>
          <div class="text-sm text-red-700"><strong>{{ payment.error_code }}</strong> - {{ payment.error_message }}</div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
