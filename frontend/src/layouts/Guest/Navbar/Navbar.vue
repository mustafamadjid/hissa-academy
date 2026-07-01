<script setup>
import { guestMenu } from "../navbar-menu";
import logo from "@/assets/images/logo.webp";
</script>
<template>
  <header class="bg-background">
    <nav
      class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between gap-6 px-5 sm:px-8 lg:px-10"
      aria-label="Navigasi utama"
    >
      <RouterLink
        :to="{ name: 'guest-home' }"
        class="shrink-0"
        aria-label="HISSA Academy - Beranda"
      >
        <img
          :src="logo"
          alt="HISSA Academy"
          class="h-10 w-auto object-contain sm:h-12"
        />
      </RouterLink>

      <div
        class="hidden h-full items-center justify-center gap-7 md:flex lg:gap-9"
      >
        <RouterLink
          v-for="item in guestMenu"
          :key="item.routeName"
          :to="{ name: item.routeName }"
          class="relative flex h-full items-center whitespace-nowrap text-sm font-medium text-neutral-high transition-colors after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:rounded-full after:bg-primary-green after:transition-[width] after:duration-300 hover:text-primary-dark-green hover:after:w-full"
          active-class="font-semibold !text-primary-dark-green after:!w-full"
        >
          <span>{{ item.label }}</span>
        </RouterLink>
      </div>

      <RouterLink
        :to="{ name: 'guest-courses' }"
        class="inline-flex shrink-0 items-center justify-center rounded-full bg-primary-dark-green px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-green focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-dark-green sm:px-7"
      >
        Mulai Belajar
      </RouterLink>
    </nav>
  </header>
</template>
