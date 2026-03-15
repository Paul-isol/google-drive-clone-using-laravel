<script setup lang="ts">
import { FolderUp } from 'lucide-vue-next';
import { ref } from 'vue';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { emitter, FOLDER_UPLOAD_EVENT } from '@/event-bus';

const folderInput = ref<HTMLInputElement | null>(null);

function triggerUpload() {
    folderInput.value?.click();
}

function onFolderChange(e: Event) {
    const files = (e.target as HTMLInputElement).files;
    emitter.emit(FOLDER_UPLOAD_EVENT, files);
}
</script>

<template>
    <DropdownMenuItem class="p-2" @select.prevent @pointerdown="triggerUpload">
        <FolderUp />
        Upload Folder
        <input
            type="file"
            ref="folderInput"
            webkitdirectory
            directory
            multiple
            hidden
            @change="onFolderChange"
        />
    </DropdownMenuItem>
</template>