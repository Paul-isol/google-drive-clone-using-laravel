<script setup lang="ts">
import { FileUp } from 'lucide-vue-next';
import { ref } from 'vue'
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { emitter, FILE_UPLOAD_EVENT } from '@/event-bus';

const fileInput = ref<HTMLInputElement | null>(null)

function triggerUpload() {
    fileInput.value?.click()
}

function onFileChange(e: Event) {
    const files = (e.target as HTMLInputElement).files
    emitter.emit(FILE_UPLOAD_EVENT, files)
}
</script>
<template>
    <DropdownMenuItem class="p-2" @select.prevent @pointerdown="triggerUpload">
        <FileUp />
        Upload File
        <input type="file" ref="fileInput" hidden multiple @change="onFileChange" />
    </DropdownMenuItem>
</template>
