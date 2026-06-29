<script setup lang="ts">
import { ref } from 'vue'
import { LogOut } from '@lucide/vue'
import { useRouter } from 'vue-router'

import { useAuthStore } from '@/features/auth/stores/auth.store'
import { adminMenu, adminMenuFooter } from '../admin-menu'
import SidebarHeader from './SidebarHeader.vue'

const authStore = useAuthStore()
const router = useRouter()
const isSigningOut = ref(false)

async function handleSignOut(): Promise<void> {
  if (isSigningOut.value) return

  isSigningOut.value = true

  try {
    await authStore.signOut()
  } catch {
   
  } finally {
    await router.replace({ name: 'administrator-login' })
    isSigningOut.value = false
  }
}
</script>

<template>
  <aside
    data-testid="admin-sidebar"
    class="flex h-screen w-53 shrink-0 flex-col border-r border-neutral-low bg-background px-2.5 py-5 text-neutral-high"
  >
    <SidebarHeader />

    <nav aria-label="Admin menu" class="mt-3 flex flex-col gap-1">
      <RouterLink
        v-for="item in adminMenu"
        :key="item.routeName"
        :to="{ name: item.routeName }"
        class="flex h-10 items-center gap-3 rounded-lg px-3 text-[13px] font-medium text-neutral-high"
        active-class="bg-primary-dark-green !text-white"
      >
        <component
          :is="item.icon"
          :size="17"
          :stroke-width="1.8"
          aria-hidden="true"
        />
        <span>{{ item.label }}</span>
      </RouterLink>
    </nav>

    <nav
      data-testid="sidebar-footer"
      aria-label="Support menu"
      class="mt-auto flex flex-col gap-1 border-t border-neutral-low pt-5"
    >
      <RouterLink
        v-for="item in adminMenuFooter"
        :key="item.routeName"
        :to="{ name: item.routeName }"
        class="flex h-10 items-center gap-3 rounded-lg px-3 text-[12px] font-medium text-neutral-high"
        active-class="bg-primary-dark-green !text-white"
      >
        <component
          :is="item.icon"
          :size="17"
          :stroke-width="1.8"
          aria-hidden="true"
        />
        <span>{{ item.label }}</span>
      </RouterLink>

      <button
        type="button"
        data-testid="sidebar-logout"
        class="flex h-10 w-full items-center gap-3 rounded-lg px-3 text-left text-[12px] font-medium text-neutral-high hover:bg-neutral-low disabled:cursor-not-allowed disabled:opacity-60"
        :disabled="isSigningOut"
        @click="handleSignOut"
      >
        <LogOut :size="17" :stroke-width="1.8" aria-hidden="true" />
        <span>{{ isSigningOut ? 'Signing out...' : 'Logout' }}</span>
      </button>
    </nav>
  </aside>
</template>
