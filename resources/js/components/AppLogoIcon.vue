<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <!-- Google Drive Icon -->
    <img src="/Google_Drive_icon.svg" alt="Google Drive Icon" />
</template>
