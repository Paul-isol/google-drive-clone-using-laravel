<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { FileUp, X } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { upload } from '@/actions/App/Http/Controllers/FileController';
import { Progress } from '@/components/ui/progress';
import { emitter, FILE_UPLOAD_EVENT, FOLDER_UPLOAD_EVENT } from '@/event-bus';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
const page = usePage()
const fileUploadForm = useForm<{
    files: any[];
    relative_path: string[];
    parent_id: number | null;
}>({
    files: [],
    relative_path: [],
    parent_id: null,
})

const isUploading = ref(false)
const uploadProgress = ref(0)
const totalFilesCount = ref(0)

function uploadFile(files: any) {
   fileUploadForm.parent_id = page.props.folder?.data.id ?? null
   fileUploadForm.files = files
   totalFilesCount.value = files.length

   fileUploadForm.post(upload().url, {
    onStart: () => {
        isUploading.value = true
        uploadProgress.value = 0
    },
    onProgress: (event) => {
        if (event.percentage) {
            uploadProgress.value = event.percentage
        }
    },
    onSuccess: () => {
        fileUploadForm.reset()
    },
    onFinish: () => {
        isUploading.value = false
    }
   })
}

function uploadFolder(files: File[]) {
   fileUploadForm.parent_id = page.props.folder.data.id ?? null
   fileUploadForm.files = files
   fileUploadForm.relative_path = [...files].map(f => f.webkitRelativePath);
   totalFilesCount.value = files.length

   fileUploadForm.post(upload().url, {
    onStart: () => {
        isUploading.value = true
        uploadProgress.value = 0
    },
    onProgress: (event) => {
        if (event.percentage) {
            uploadProgress.value = event.percentage
        }
    },
    onSuccess: () => {
        fileUploadForm.reset()
    },
    onFinish: () => {
        isUploading.value = false
    }
   })
}

onMounted(() => {
    emitter.on(FILE_UPLOAD_EVENT, uploadFile)
    emitter.on(FOLDER_UPLOAD_EVENT, uploadFolder)
})
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />

        <!-- Upload Progress Overlay -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-full opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-full opacity-0"
        >
            <div v-if="isUploading" class="fixed bottom-6 right-6 z-50 w-80">
                <div class="bg-card text-card-foreground rounded-xl border p-4 shadow-2xl ring-1 ring-black/5 dark:ring-white/10">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="bg-primary/10 flex h-8 w-8 items-center justify-center rounded-lg text-primary">
                                <FileUp class="h-4 w-4 animate-bounce" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold tracking-tight">Uploading files...</h3>
                                <p class="text-muted-foreground text-[10px] uppercase tracking-wider font-bold">
                                    {{ totalFilesCount }} {{ totalFilesCount === 1 ? 'file' : 'files' }}
                                </p>
                            </div>
                        </div>
                        <button @click="isUploading = false" class="text-muted-foreground hover:text-foreground transition-colors">
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="space-y-2">
                        <Progress :model-value="uploadProgress" class="h-1.5" />
                        <div class="flex justify-between text-[11px] font-medium text-muted-foreground">
                            <span>{{ Math.round(uploadProgress) }}% complete</span>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>
