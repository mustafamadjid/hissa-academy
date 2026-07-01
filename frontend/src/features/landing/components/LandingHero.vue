<script setup lang="ts">
import {
  CirclePlay,
  TrendingUp,
  Verified,
  ShieldCheck,
  LayoutGrid,
  ArrowRight,
  ChartBar,
} from "@lucide/vue";
import mentorImage from "@/assets/images/landing-page/landing-1.png";

const features = [
  {
    icon: ShieldCheck,
    title: "100% Syariah",
    desc: "Sesuai Fatwa DSN-MUI",
    color: "text-primary-green",
  },
  {
    icon: TrendingUp,
    title: "Analisis Riil",
    desc: "Studi kasus pasar modal",
    color: "text-primary-green",
  },
  {
    icon: Verified,
    title: "Sertifikasi",
    desc: "Diakui oleh industri",
    color: "text-primary-green",
  },
];
</script>

<template>
  <section
    class="relative min-h-[calc(100vh-80px)] overflow-hidden bg-[#F8FAFC]"
  >
    <!-- Ornamen Background Super Soft -->
    <div
      class="absolute -left-24 -top-24 h-96 w-96 rounded-full bg-primary-green/5 blur-3xl"
    />
    <div
      class="absolute right-0 top-1/2 h-64 w-64 -translate-y-1/2 rounded-full bg-lime-accent/10 blur-3xl"
    />

    <!-- Pola Dot Matrix Dekoratif -->
    <div
      class="absolute inset-0 z-0 opacity-[0.03]"
      style="
        background-image: radial-gradient(#0b4129 1px, transparent 1px);
        background-size: 30px 30px;
      "
    ></div>

    <div
      class="container relative z-10 mx-auto grid min-h-[calc(100vh-80px)] lg:grid-cols-2"
    >
      <!-- SISI KIRI: KONTEN TEKS -->
      <div class="flex flex-col justify-center px-6 py-12 lg:px-12 xl:px-16">
        <!-- Badge -->
        <div class="mb-6 flex">
          <div
            class="inline-flex items-center gap-2 rounded-full bg-white border border-slate-200 px-4 py-1.5 text-[12px] font-bold tracking-wide text-primary-dark-green shadow-sm"
          >
            <span class="flex h-2 w-2 rounded-full bg-primary-green"></span>
            AKADEMI INVESTASI SYARIAH #1 DI INDONESIA
          </div>
        </div>

        <!-- Headline -->
        <h1
          class="text-4xl font-extrabold leading-[1.1] tracking-tight text-slate-900 sm:text-6xl xl:text-7xl"
        >
          Investasi Berkah, <br />
          <span class="relative inline-block text-primary-dark-green">
            Masa Depan
            <svg
              class="absolute -bottom-3 left-0 w-full"
              viewBox="0 0 300 12"
              fill="none"
            >
              <path
                d="M2 10C50 2 150 2 298 10"
                stroke="#bef264"
                stroke-width="6"
                stroke-linecap="round"
              />
            </svg>
          </span>
          <br />Cerah Tanpa Riba.
        </h1>

        <p class="mt-8 max-w-lg text-lg leading-relaxed text-slate-600">
          Ubah mindset spekulasi menjadi investasi yang terukur. Pelajari
          metodologi pemilihan saham syariah yang menguntungkan dunia dan
          akhirat.
        </p>

        <!-- Action Buttons -->
        <div class="mt-10 flex flex-wrap items-center gap-4">
          <RouterLink
            :to="{ name: 'guest-courses' }"
            class="group flex items-center gap-2 rounded-2xl bg-primary-green px-8 py-4 text-lg font-bold text-white transition-all hover:-translate-y-1 hover:bg-primary-dark-green"
          >
            Mulai Belajar
            <ArrowRight
              class="h-5 w-5 transition-transform group-hover:translate-x-1"
            />
          </RouterLink>

          <button
            type="button"
            class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-6 py-4 font-bold text-slate-700 transition-all hover:bg-slate-50"
          >
            <div
              class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-green/10 text-primary-green transition-colors group-hover:bg-primary-green group-hover:text-white"
            >
              <CirclePlay :size="20" />
            </div>
            Intip Kurikulum
          </button>
        </div>

        <!-- Benefits -->
        <div
          class="mt-14 grid grid-cols-1 gap-4 border-t border-slate-200 pt-10 sm:grid-cols-3"
        >
          <div
            v-for="item in features"
            :key="item.title"
            class="flex flex-col gap-2"
          >
            <div
              :class="`flex h-10 w-10 items-center justify-center rounded-xl ${item.color}`"
            >
              <component :is="item.icon" :size="20" />
            </div>
            <div>
              <h4 class="text-sm font-bold text-slate-900">{{ item.title }}</h4>
              <p class="text-xs text-slate-500">{{ item.desc }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- SISI KANAN: VISUAL DENGAN FLOATING CARDS -->
      <div class="relative flex items-center justify-center p-6 lg:p-12">
        <!-- Background Lingkaran Besar -->
        <div
          class="absolute h-[70%] w-[70%] rounded-full bg-primary-dark-green shadow-inner"
        />

        <div class="relative z-10 w-full max-w-md">
          <!-- Bingkai Foto Utama -->
          <div
            class="relative overflow-hidden rounded-[48px] border-12 border-white shadow-2xl"
          >
            <img
              :src="mentorImage"
              alt="Mentor"
              class="h-full w-full object-cover"
            />
          </div>

          <!-- Floating Card 3: Experience -->
          <div
            class="absolute -bottom-6 left-1/2 -translate-x-1/2 flex w-max items-center gap-3 rounded-full border border-white bg-white/90 px-6 py-3 shadow-lg backdrop-blur-md"
          >
            <LayoutGrid :size="18" class="text-primary-green" />
            <span class="text-sm font-bold text-slate-800"
              >12+ Modul Praktis Berbasis Industri</span
            >
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Transisi Halus */
h1 {
  text-wrap: balance;
}

img {
  filter: grayscale(10%) contrast(105%);
}
</style>
