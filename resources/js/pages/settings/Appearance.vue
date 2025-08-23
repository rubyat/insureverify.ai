<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { computed } from 'vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: '/settings/appearance',
    },
];
const page = usePage();
const Layout = computed(() => (page.props as any)?.auth?.is_admin ? AppLayout : SiteLayout);
</script>

<template>
    <component :is="Layout" :breadcrumbs="breadcrumbItems">
        <Head title="Appearance settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Appearance settings" description="Update your account's appearance settings" />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </component>
</template>
