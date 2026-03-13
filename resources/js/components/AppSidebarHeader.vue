<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import {
    InputGroup,
    InputGroupAddon,
    InputGroupInput,
} from '@/components/ui/input-group';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
const form = useForm({
    search: '',
});
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-4 border-b border-sidebar-border/70 px-4 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12"
    >
        <!-- Left: trigger + breadcrumbs -->
        <div class="flex min-w-0 items-center gap-2">
            <SidebarTrigger class="-ml-1 shrink-0" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Right: search bar -->
        <div class="ml-auto flex items-center">
            <InputGroup class="w-56 lg:w-72">
                <InputGroupInput
                    placeholder="Search..."
                    v-model="form.search"
                    autocomplete
                />
                <InputGroupAddon>
                    <Search class="size-4" />
                </InputGroupAddon>
            </InputGroup>
        </div>
    </header>
</template>
