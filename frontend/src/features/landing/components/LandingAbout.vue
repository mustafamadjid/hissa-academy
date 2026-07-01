<script setup lang="ts">
import { Check, GraduationCap, Star } from "@lucide/vue";
import instructorImage from "@/assets/images/landing-page/landing-2.png";

const benefits = [
  "Sistem Pendukung Unik",
  "Dukungan Seumur Hidup",
  "Dapatkan Sertifikat",
  "Instruktur Luar Biasa",
];
</script>

<template>
  <section class="bg-white px-5 py-20 sm:px-8 lg:py-28">
    <div
      class="mx-auto grid max-w-7xl items-center gap-14 lg:grid-cols-2 lg:gap-20"
    >
      <div class="relative mx-auto w-full max-w-xl">
        <div
          class="absolute -left-6 -top-6 h-32 w-32 rounded-full bg-lime-accent/30 blur-2xl"
        />
        <div
          class="relative aspect-square overflow-hidden rounded-4xl shadow-2xl"
        >
          <img
            :src="instructorImage"
            alt="Instruktur HISSA Academy"
            class="h-full w-full object-cover"
          />
          <div
            class="glass-card absolute bottom-6 left-6 right-6 flex items-center gap-4 rounded-2xl p-5"
          >
            <span class="rounded-full bg-primary-green p-3 text-white"
              ><Star :size="22" fill="currentColor"
            /></span>
            <span
              ><strong class="block text-primary-dark-green"
                >Dipercaya 10rb+</strong
              ><small class="text-neutral-medium"
                >Mitra Sertifikasi Resmi</small
              ></span
            >
          </div>
        </div>
      </div>
      <div>
        <p
          class="mb-5 flex items-center gap-2 text-xs font-bold tracking-wider text-primary-green"
        >
          <GraduationCap :size="20" /> TENTANG KAMI
        </p>
        <h2
          class="text-3xl font-bold leading-tight text-primary-dark-green sm:text-4xl"
        >
          Mengedukasi masyarakat untuk memajukan bangsa
        </h2>
        <p class="mt-6 text-base leading-8 text-neutral-medium sm:text-lg">
          Kami membangun kepercayaan melalui pendidikan berkualitas, pengakuan
          yang teruji, serta kolaborasi dengan para pemimpin industri. HISSA
          Academy membantu setiap pembelajar memahami keuangan syariah secara
          relevan dan terarah.
        </p>
        <div class="mt-8 grid gap-5 sm:grid-cols-2">
          <div
            v-for="benefit in benefits"
            :key="benefit"
            class="flex items-center gap-3 font-semibold text-primary-dark-green"
          >
            <span class="rounded-full bg-primary-green p-1 text-white"
              ><Check :size="14" stroke-width="3" /></span
            >{{ benefit }}
          </div>
        </div>
        <RouterLink
          :to="{ name: 'guest-about' }"
          class="mt-9 inline-flex rounded-xl bg-primary-dark-green px-8 py-4 font-semibold text-white transition hover:-translate-y-1 hover:shadow-lg"
          >Jelajahi Visi Kami</RouterLink
        >
      </div>
    </div>
  </section>
</template>

<style scoped>
.glass-card {
  background: rgb(255 255 255 / 0.8);
  backdrop-filter: blur(12px);
}
</style>
