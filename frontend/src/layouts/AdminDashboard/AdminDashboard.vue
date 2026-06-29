<script setup lang="ts">
import Sidebar from "./Sidebar/Sidebar.vue";
</script>

<template>
  <div
    data-testid="admin-dashboard-layout"
    class="flex h-screen min-w-0 overflow-hidden bg-background"
  >
    <Sidebar />

    <main
      data-testid="admin-dashboard-content"
      class="min-w-0 flex-1 overflow-y-auto"
    >
      <div class="mx-auto min-h-full w-full max-w-max-width p-6 lg:p-8">
        <slot />
      </div>
    </main>
  </div>
</template>
