<template>
    <component :is="ResponsiveModal.Root" v-model:open="isOpen">
        <component
            :is="ResponsiveModal.Content"
            class="sm:max-w-md"
            :class="[{ 'px-2 pb-8 *:px-4': !isDesktop }]"
        >
            <component :is="ResponsiveModal.Header">
                <component :is="ResponsiveModal.Title">
                    Create New Folder
                </component>
                <component :is="ResponsiveModal.Description">
                    Enter the name for your new folder.
                </component>
            </component>
            <div class="flex items-center gap-2">
                <div class="grid flex-1 gap-2">
                    <Label for="folder-name" class="sr-only">
                        Folder Name
                    </Label>
                    <Input
                        ref="folderNameInput"
                        id="folder-name"
                        v-model="form.name"
                        placeholder="Folder name"
                        @keyup.enter="createFolder"
                        :class="
                            form.errors.name
                                ? 'border-red-500 focus-visible:ring-red-500'
                                : ''
                        "
                    />
                    <InputError :message="form.errors.name" />
                </div>
            </div>
            <component :is="ResponsiveModal.Footer" class="pt-4">
                <component :is="ResponsiveModal.Close" as-child>
                    <Button variant="outline"> Close </Button>
                </component>
                <Button @click="createFolder" :disabled="form.processing">
                    <Spinner v-if="form.processing" class="mr-2 h-4 w-4" />
                    Create
                </Button>
            </component>
        </component>
    </component>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useMediaQuery } from '@vueuse/core';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerFooter,
    DrawerHeader,
    DrawerTitle,
} from '@/components/ui/drawer';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from './InputError.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Spinner from './ui/spinner/Spinner.vue';
import { createFolder as createFolderAction } from '@/actions/App/Http/Controllers/FileController';
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const isDesktop = useMediaQuery('(min-width: 640px)');
const isOpen = ref(false);

const form = useForm<{
    name: string;
    parent_id: number | null;
}>({
    name: '',
    parent_id: null,
});
const page = usePage();
function createFolder() {
    form.parent_id = page.props.folder?.id ?? null;
    form.post(createFolderAction().url, {
        preserveScroll: true,
        onSuccess: () => {
            isOpen.value = false;
            form.reset();
        },
    });
}
watch(
    () => props.modelValue,
    (newVal) => {
        isOpen.value = newVal;
    },
);

watch(isOpen, (newVal) => {
    emit('update:modelValue', newVal);
});

const ResponsiveModal = computed(() => ({
    Root: isDesktop.value ? Dialog : Drawer,
    Content: isDesktop.value ? DialogContent : DrawerContent,
    Header: isDesktop.value ? DialogHeader : DrawerHeader,
    Title: isDesktop.value ? DialogTitle : DrawerTitle,
    Description: isDesktop.value ? DialogDescription : DrawerDescription,
    Footer: isDesktop.value ? DialogFooter : DrawerFooter,
    Close: isDesktop.value ? DialogClose : DrawerClose,
}));
</script>
