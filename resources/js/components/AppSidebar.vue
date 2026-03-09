<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    BookOpen,
    FolderGit2,
    Star,
    Trash,
    Users,
    Folder,
    FolderPlus,
    FolderUp,
    FileUp,
    Plus,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup,
} from '@/components/ui/sidebar';
import { dashboard, myfiles, sharedByMe, sharedWithMe, trash } from '@/routes';
import type { NavItem } from '@/types';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from './ui/dropdown-menu';
import CreateFolderModal from './CreateFolderModal.vue';
const mainNavItems: NavItem[] = [
    {
        title: 'My Drive',
        href: myfiles(),
        icon: Folder,
    },
    {
        title: 'Shared with me',
        href: sharedWithMe(),
        icon: Users,
    },
    {
        title: 'Starred',
        href: sharedByMe(),
        icon: Star,
    },
    {
        title: 'Trash',
        href: trash(),
        icon: Trash,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/Paul-isol',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

const createFolderModal = ref(false);
function showCreateFolderModal() {
    createFolderModal.value = true;
}
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarMenu>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarGroup class="px-2 py-0">
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <SidebarMenuButton
                                    class="border-2 border-white p-4"
                                >
                                    <Plus />Create New
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroup>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start" class="w-56">
                    <a href="#" @click.prevent="showCreateFolderModal()">
                        <DropdownMenuItem class="p-2">
                            <FolderPlus />
                            New Folder
                        </DropdownMenuItem>
                    </a>

                    <DropdownMenuItem class="p-2">
                        <FolderUp />
                        Upload Folder
                    </DropdownMenuItem>
                    <DropdownMenuItem class="p-2">
                        <FileUp />
                        Upload Files
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenu>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <CreateFolderModal v-model="createFolderModal" />
    <slot />
</template>
