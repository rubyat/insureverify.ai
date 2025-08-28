<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, CreditCard, Users, BarChart3, UserPlus, Activity, Wallet, FileText, Settings, Layers } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    // {
    //     title: 'Billing',
    //     href: '/billing',
    //     icon: CreditCard,
    // },
];

const footerNavItems: NavItem[] = [];

// Admin navigation links
const adminNavItems: NavItem[] = [
  { title: 'Plans', href: '/admin/plans', icon: Layers },
  { title: 'Users', href: '/admin/users', icon: Users },
  { title: 'Reports', href: '/admin/reports', icon: BarChart3 },
  { title: 'Subscribers', href: '/admin/subscribers', icon: UserPlus },
  { title: 'Billing', href: '/admin/billing', icon: CreditCard },
  { title: 'Usage', href: '/admin/usage', icon: Activity },
  { title: 'Payments', href: '/admin/payments', icon: Wallet },
  { title: 'Pages', href: '/admin/pages', icon: FileText },
  { title: 'Settings', href: '/admin/settings', icon: Settings },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('admin.dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <div class="mt-4">
                <NavMain :items="adminNavItems" />
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
