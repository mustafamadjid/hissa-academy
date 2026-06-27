<script setup lang="ts">
import { Plus } from "@lucide/vue";

import { adminMenu, adminMenuFooter } from "../admin-menu";
import SidebarHeader from "./SidebarHeader.vue";

const activeMenu = "Courses";
</script>

<template>
  <aside
    data-testid="admin-sidebar"
    class="flex h-screen w-53 shrink-0 flex-col border-r border-neutral-low bg-background px-2.5 py-5 text-neutral-high"
  >
    <SidebarHeader />

    
    <nav aria-label="Admin menu" class="mt-5 flex flex-col gap-1">
      <div
        v-for="item in adminMenu"
        :key="item.routeName"
        :aria-current="item.label === activeMenu ? 'page' : undefined"
        class="flex h-10 items-center gap-3 rounded-lg px-3 text-[12px] font-medium"
        :class="
          item.label === activeMenu
            ? 'bg-emerald-200 text-primary-dark-green'
            : 'text-neutral-high'
        "
      >
        <component
          :is="item.icon"
          :size="17"
          :stroke-width="1.8"
          aria-hidden="true"
        />
        <span>{{ item.label }}</span>
      </div>
    </nav>

    <nav
      data-testid="sidebar-footer"
      aria-label="Support menu"
      class="mt-auto flex flex-col gap-1 border-t border-neutral-low pt-5"
    >
      <div
        v-for="item in adminMenuFooter"
        :key="item.routeName"
        class="flex h-10 items-center gap-3 rounded-lg px-3 text-[12px] font-medium text-neutral-high"
      >
        <component
          :is="item.icon"
          :size="17"
          :stroke-width="1.8"
          aria-hidden="true"
        />
        <span>{{ item.label }}</span>
      </div>
    </nav>
  </aside>
</template>
