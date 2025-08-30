<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import PagesTemplateBuilder from '@/pages/Admin/Pages/TemplateBuilder.vue'

const props = defineProps<{
  page: any
  template: Record<string, any>
  blocksEndpoint: string
  livePreviewEndpoint: string
  saveEndpoint: string
  blocksThumbEndpoint: string
}>()
</script>

<template>
  <Head :title="`Builder: ${props.page?.title || 'Blog'}`" />
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Blogs', href: route('admin.blogs.index') },
    { title: `Builder: ${props.page?.title}` , href: '#' }
  ]">
    <div class="p-6">
      <PagesTemplateBuilder
        :page="props.page"
        :template="props.template"
        :blocks-endpoint="props.blocksEndpoint"
        :live-preview-endpoint="props.livePreviewEndpoint"
        :save-endpoint="props.saveEndpoint"
        :blocks-thumb-endpoint="props.blocksThumbEndpoint"
      />
    </div>
  </AppLayout>
</template>
