<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"
import { Input } from '@/components/ui/input'

const props = defineProps<{
  modelValue: string
  class?: HTMLAttributes["class"]
}>()

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <Input
    data-slot="input-group-control"
    :class="cn(
      'flex-1 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 dark:bg-transparent',
      props.class,
    )"
    :value="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
  />
</template>
