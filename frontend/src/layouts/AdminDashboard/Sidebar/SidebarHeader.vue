<script setup lang="ts">
import { GraduationCap } from '@lucide/vue'
</script>

<template>
  <header class="flex items-center gap-3 px-3">
    <div
      class="flex size-8 shrink-0 items-center justify-center rounded-md bg-primary-dark-green text-white"
    >
      <GraduationCap :size="18" :stroke-width="1.8" aria-hidden="true" />
    </div>

    <div class="min-w-0">
      <h1 class="truncate text-[14px] font-bold leading-5 text-primary-dark-green">
        HISSA Academy
      </h1>
      <p class="text-[10px] leading-4 text-neutral-medium">Admin Console</p>
    </div>
  </header>
</template>
