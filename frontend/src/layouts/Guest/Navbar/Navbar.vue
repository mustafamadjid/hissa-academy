<script setup lang="ts">
import { ref } from "vue";
import { Menu, X } from "@lucide/vue";

import { guestMenu } from "../navbar-menu";
import logo from "@/assets/images/logo.webp";

const isMobileMenuOpen = ref(false);

function toggleMobileMenu(): void {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
}

function closeMobileMenu(): void {
  isMobileMenuOpen.value = false;
}
</script>

<template>
  <header
    class="fixed inset-x-0 top-0 z-50 border-b border-neutral-low/70 bg-white/90 backdrop-blur-xl"
  >
    <nav
      class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between px-5 sm:px-8 lg:px-10"
      aria-label="Navigasi utama"
    >
      <!-- Logo -->
      <RouterLink
        :to="{ name: 'guest-home' }"
        class="flex shrink-0 items-center"
        aria-label="HISSA Academy - Beranda"
        @click="closeMobileMenu"
      >
        <img
          :src="logo"
          alt="HISSA Academy"
          class="h-12 w-auto object-contain sm:h-14"
        />
      </RouterLink>

      <!-- Desktop Navigation -->
      <div
        class="absolute left-1/2 hidden h-full -translate-x-1/2 items-center md:flex"
      >
        <div class="flex h-full items-center gap-8 lg:gap-10">
          <RouterLink
            v-for="item in guestMenu"
            :key="item.routeName"
            :to="{ name: item.routeName }"
            class="group relative flex h-full items-center whitespace-nowrap px-1 text-sm font-medium text-neutral-medium transition-colors duration-200 hover:text-primary-dark-green lg:text-base"
            active-class="!font-semibold !text-primary-dark-green"
          >
            <span>{{ item.label }}</span>

            <span
              class="absolute bottom-4 left-1/2 h-0.5 w-0 -translate-x-1/2 rounded-full bg-primary-green transition-all duration-300 ease-out group-hover:w-full"
            />

            <span
              class="absolute bottom-4 left-1/2 h-0.5 w-0 -translate-x-1/2 rounded-full bg-primary-green router-link-active:w-full"
            />
          </RouterLink>
        </div>
      </div>

      <!-- Desktop CTA -->
      <div class="hidden items-center md:flex">
        <RouterLink
          :to="{ name: 'guest-courses' }"
          class="inline-flex items-center justify-center rounded-full bg-primary-dark-green px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary-green hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-dark-green"
        >
          Mulai Belajar
        </RouterLink>
      </div>

      <!-- Mobile Menu Button -->
      <button
        type="button"
        class="inline-flex size-11 items-center justify-center rounded-xl border border-neutral-low bg-white text-neutral-high transition-colors hover:bg-surface-dim md:hidden"
        :aria-expanded="isMobileMenuOpen"
        aria-controls="mobile-navigation"
        aria-label="Buka menu navigasi"
        @click="toggleMobileMenu"
      >
        <X v-if="isMobileMenuOpen" class="size-5" />
        <Menu v-else class="size-5" />
      </button>
    </nav>

    <!-- Mobile Navigation -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="-translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="-translate-y-2 opacity-0"
    >
      <div
        v-if="isMobileMenuOpen"
        id="mobile-navigation"
        class="border-t border-neutral-low bg-white px-5 pb-6 pt-3 shadow-lg md:hidden"
      >
        <div class="mx-auto flex max-w-7xl flex-col">
          <RouterLink
            v-for="item in guestMenu"
            :key="item.routeName"
            :to="{ name: item.routeName }"
            class="group relative flex items-center py-4 text-base font-medium text-neutral-medium transition-colors hover:text-primary-dark-green"
            active-class="!font-semibold !text-primary-dark-green"
            @click="closeMobileMenu"
          >
            <span>{{ item.label }}</span>

            <span
              class="absolute bottom-2 left-0 h-0.5 w-0 rounded-full bg-primary-green transition-all duration-300 group-hover:w-10"
            />

            <span
              class="absolute bottom-2 left-0 h-0.5 w-0 rounded-full bg-primary-green router-link-active:w-10"
            />
          </RouterLink>

          <RouterLink
            :to="{ name: 'guest-courses' }"
            class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-primary-dark-green px-6 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-primary-green"
            @click="closeMobileMenu"
          >
            Mulai Belajar
          </RouterLink>
        </div>
      </div>
    </Transition>
  </header>
</template>
