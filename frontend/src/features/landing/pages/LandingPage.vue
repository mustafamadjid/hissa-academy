<script setup lang="ts">
import GuestLayout from "@/layouts/Guest/GuestLayout.vue";
import LandingAbout from "../components/LandingAbout.vue";
import LandingCategories from "../components/LandingCategories.vue";
import LandingCta from "../components/LandingCta.vue";
import LandingFooter from "../components/LandingFooter.vue";
import LandingHero from "../components/LandingHero.vue";
</script>

<template>
  <GuestLayout>
    <div class="overflow-x-hidden bg-background text-neutral-high">
      <LandingHero />
      <LandingCategories />
      <LandingAbout />
      <LandingCta />
      <LandingFooter />
    </div>
  </GuestLayout>
</template>
