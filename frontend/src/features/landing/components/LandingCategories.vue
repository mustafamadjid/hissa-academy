<script setup lang="ts">
import {
  ArrowRight,
  Banknote,
  HandHeart,
  Landmark,
  NotebookPen,
  Layers,
} from "@lucide/vue";

const categories = [
  {
    title: "Saham Halal",
    description:
      "Pelajari dasar-dasar dalam mengidentifikasi dan menganalisis ekuitas yang sesuai Syariah.",
    icon: Banknote,
    count: "12+ Materi",
    color: "text-blue-600",
    bgColor: "bg-blue-50",
  },
  {
    title: "Perbankan Etis",
    description:
      "Memahami prinsip-prinsip perbankan Islam dan model keuangan tanpa bunga.",
    icon: Landmark,
    featured: true,
    count: "08+ Materi",
    color: "text-emerald-600",
    bgColor: "bg-emerald-50",
  },
  {
    title: "Financial Plan",
    description:
      "Manajemen kekayaan strategis sesuai dengan prinsip Maqasid al-Shari'ah.",
    icon: NotebookPen,
    count: "15+ Materi",
    color: "text-orange-600",
    bgColor: "bg-orange-50",
  },
  {
    title: "Filantropi",
    description:
      "Mengoptimalkan Zakat, Wakaf, dan Sedekah untuk dampak sosial berkelanjutan.",
    icon: HandHeart,
    count: "10+ Materi",
    color: "text-purple-600",
    bgColor: "bg-purple-50",
  },
];
</script>

<template>
  <section
    class="relative overflow-hidden bg-[#f8fafc] px-5 py-20 sm:px-8 lg:py-28"
  >
    <!-- Elemen Dekoratif Background -->
    <div
      class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-primary-green/5 blur-3xl"
    ></div>
    <div
      class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-emerald-500/5 blur-3xl"
    ></div>

    <div class="relative mx-auto max-w-7xl">
      <!-- Header Section -->
      <div class="mx-auto mb-16 max-w-3xl text-center">
        <span
          class="inline-block rounded-full bg-primary-green px-4 py-1.5 text-[11px] font-bold uppercase tracking-widest text-white                                 shadow-sm"
        >
          KATALOG HISSA ACADEMY
        </span>
        <h2
          class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl"
        >
          Eksplorasi
          <span class="text-primary-dark-green">Kategori Kursus</span> Kami
        </h2>
        <p class="mt-6 text-lg leading-relaxed text-slate-600">
          Kurikulum yang dirancang khusus untuk membantu Anda menguasai
          ekosistem keuangan Syariah secara profesional dan komprehensif.
        </p>
      </div>

      <!-- Grid Kategori -->
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <article
          v-for="(category, index) in categories"
          :key="category.title"
          class="group relative flex flex-col rounded-[2.5rem] border border-white bg-white p-8 transition-all duration-500 hover:-translate-y-3 hover:shadow-[0_20px_50px_rgba(0,0,0,0.05)]"
          :class="{
            'ring-2 ring-primary-dark-green ring-offset-4': category.featured,
          }"
        >
          <!-- Badge untuk Featured -->
          <div
            v-if="category.featured"
            class="absolute -top-4 right-8 rounded-full bg-primary-dark-green px-4 py-1 text-[10px] font-bold uppercase text-white shadow-lg"
          >
            Terpopuler
          </div>

          <!-- Icon Container -->
          <div
            class="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl transition-transform duration-500 group-hover:scale-110 shadow-sm"
            :class="
              category.featured
                ? 'bg-primary-green text-white'
                : 'bg-slate-50 text-emerald-600 border border-slate-100'
            "
          >
            <component :is="category.icon" :size="32" stroke-width="1.5" />
          </div>

          <!-- Content -->
          <div
            class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400"
          >
            <Layers :size="12" />
            {{ category.count }}
          </div>

          <h3 class="mb-3 text-2xl font-bold text-slate-800">
            {{ category.title }}
          </h3>

          <p class="mb-8 text-sm leading-relaxed text-slate-500">
            {{ category.description }}
          </p>

          <!-- Action -->
          <RouterLink
            :to="{ name: 'guest-courses' }"
            class="mt-auto flex items-center justify-between rounded-2xl bg-slate-50 px-5 py-3 text-sm font-bold text-slate-700 transition-all group-hover:bg-primary-green group-hover:text-white"
          >
            Pelajari
            <ArrowRight
              :size="18"
              class="transition-transform group-hover:translate-x-1"
            />
          </RouterLink>
        </article>
      </div>

      <!-- Footer Section (Tambahan agar tidak kosong) -->
      <div
        class="mt-20 flex flex-wrap items-center justify-center gap-8 border-t border-slate-200 pt-10"
      >
        <p class="text-sm font-medium text-slate-400">
          Dipercaya oleh 10,000+ Pelajar di Seluruh Indonesia
        </p>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Optional: Menambahkan animasi smooth fade-in */
article {
  animation: fadeInUp 0.6s ease-out forwards;
  opacity: 0;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Delay animasi untuk tiap card */
article:nth-child(1) {
  animation-delay: 0.1s;
}
article:nth-child(2) {
  animation-delay: 0.2s;
}
article:nth-child(3) {
  animation-delay: 0.3s;
}
article:nth-child(4) {
  animation-delay: 0.4s;
}
</style>
