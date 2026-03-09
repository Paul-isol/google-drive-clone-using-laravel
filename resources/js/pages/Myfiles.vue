<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { myfiles } from '@/routes';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableRow,
    TableHeader,
} from '@/components/ui/table';
const props = defineProps<{
    files: any; // Using `any` allows accepting the full Pagination object correctly without strict type rules here.
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My files',
        href: myfiles(),
    },
];
</script>

<template>
    <Head title="My Files" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
        >
            <Table>
                <TableCaption>All Folders.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]"> Name </TableHead>
                        <TableHead>Owner</TableHead>
                        <TableHead>Last Modified</TableHead>
                        <TableHead class="text-right"> Size </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="file in files.data" :key="file.id">
                        <TableCell class="font-medium">
                            {{ file.name }}
                        </TableCell>
                        <TableCell>{{ file.owner }}</TableCell>
                        <TableCell>{{ file.updated_at }}</TableCell>
                        <TableCell class="text-right">
                            {{ file.size }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
