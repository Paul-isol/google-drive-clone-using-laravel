<script setup lang="ts">
import { Head, router, Link, InfiniteScroll } from '@inertiajs/vue3';
import { 
    Folder, 
    FileText, 
    MoreVertical, 
    Download, 
    Trash2, 
    Share2, 
    FilePlus,
    Home,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import {
  Breadcrumb,
  BreadcrumbItem as BDItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb'
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuSeparator
} from '@/components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
    TableHeader,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { myfiles } from '@/routes';
import type { BreadcrumbItem } from '@/types';
const props = defineProps<{
    files: any;
    folder: any;
    ancestors: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My files',
        href: myfiles(),
    },
];

function openFolder(file: any) {
    if (!file.is_folder) {
        return;
    }
    router.visit(myfiles({ folder: file.path }).url);
}

const selectedFiles = ref<Record<string, boolean>>({});

const allSelected = computed({
    get() {
        return props.files.data.length > 0 && props.files.data.every((file: any) => selectedFiles.value[file.id]);
    },
    set(val) {
        props.files.data.forEach((file: any) => {
            selectedFiles.value[file.id] = val;
        });
    }
});
</script>

<template>
    <Head title="My Files" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6 animate-in fade-in duration-500">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">My Files</h1>
                    <p class="text-sm text-muted-foreground">Manage and organize your files and folders.</p>
                </div>
            </div>
            <nav>
                <Breadcrumb>
                    <BreadcrumbList>
                        <Home width="16" />
                        <template v-for="ans in ancestors.data" :key="ans.id">
                            <BDItem>
                                <BreadcrumbLink as-child>
                                    <Link :href="myfiles({ folder: ans.path }).url">
                                        {{ ans.name }}
                                    </Link>
                                </BreadcrumbLink>
                            </BDItem>
                            <BreadcrumbSeparator />
                        </template>
                    </BreadcrumbList>
                </Breadcrumb>
            </nav>

            <!-- Table Container -->
            <div class="rounded-xl border bg-card shadow-sm transition-all duration-300 hover:shadow-md dark:bg-card/50">
                <InfiniteScroll data="files" items-element="#table-body">
                    <Table>
                        <TableHeader>
                        <TableRow class="hover:bg-transparent">
                            <TableHead class="w-[50px] p-4 font-medium">
                                <Checkbox v-model="allSelected" />
                            </TableHead>
                            <TableHead class="w-[400px] p-4 font-medium">Name</TableHead>
                            <TableHead class="p-4 font-medium">Owner</TableHead>
                            <TableHead class="p-4 font-medium">Last Modified</TableHead>
                            <TableHead class="p-4 font-medium">Size</TableHead>
                            <TableHead class="w-[50px] p-4 font-medium"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody id="table-body">
                        <!-- Empty State -->
                        <TableRow v-if="!files.data.length" class="hover:bg-transparent">
                            <TableCell colspan="5" class="h-96 text-center">
                                <div class="flex flex-col items-center justify-center gap-4 text-muted-foreground">
                                    <div class="rounded-full bg-muted p-6">
                                        <FilePlus class="h-10 w-10 opacity-20" />
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-lg font-medium text-foreground">No files found</p>
                                        <p class="text-sm">This folder is empty. Start by uploading a file or creating a folder.</p>
                                    </div>
                                </div>
                            </TableCell>
                        </TableRow>

                        <!-- File/Folder Rows -->
                        <TableRow 
                            v-for="file in files.data" 
                            :key="file.id" 
                            @dblclick="openFolder(file)"
                            class="group select-none transition-colors duration-200 cursor-pointer"
                            :data-state="selectedFiles[file.id] ? 'selected' : undefined"
                        >
                            <TableCell class="p-4 font-medium">
                               <Checkbox v-model="selectedFiles[file.id]" />
                            </TableCell>
                            <TableCell class="p-4 font-medium">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/5 text-primary transition-colors group-hover:bg-primary/10">
                                        <Folder v-if="file.is_folder" class="h-5 w-5 fill-primary/10" />
                                        <FileText v-else class="h-5 w-5" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium leading-none">{{ file.name }}</span>
                                        <span class="mt-1 text-xs text-muted-foreground">{{ file.is_folder ? 'Folder' : 'File' }}</span>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                <div class="flex items-center gap-2">
                                    <span class="capitalize">{{ file.owner }}</span>
                                </div>
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ file.updated_at }}
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ file.size || '--' }}
                            </TableCell>
                            <TableCell class="p-4 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon" class="h-8 w-8 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <MoreVertical class="h-4 w-4" />
                                            <span class="sr-only">Open menu</span>
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-48">
                                        <DropdownMenuItem @click="openFolder(file)" v-if="file.is_folder">
                                            Open folder
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Share2 class="mr-2 h-4 w-4" />
                                            Share
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Download class="mr-2 h-4 w-4" />
                                            Download
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem class="text-destructive focus:text-destructive">
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Remove
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                    </Table>
                </InfiniteScroll>
            </div>
        </div>
    </AppLayout>
</template>
