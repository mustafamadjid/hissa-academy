<script setup lang="ts">
import {
  ArrowRight,
  BookOpen,
  CheckCircle2,
  ChevronLeft,
  ChevronRight,
  CircleAlert,
  Clock,
  Filter,
  GraduationCap,
  LoaderCircle,
  Search,
  Sparkles,
  TrendingUp,
  Users,
  X,
} from "@lucide/vue";
import { ref } from "vue";

import heroImage from "@/assets/images/landing-page/landing-3.png";
import GuestLayout from "@/layouts/Guest/GuestLayout.vue";

import CourseCatalogCard from "@/features/course/Student/components/CourseCatalogCard.vue";
import { useCourseCatalog } from "../../course/Student/composables/useCourseCatalog.ts";

const {
  courses,
  search,
  page,
  isLoading,
  error,
  canGoBack,
  canGoForward,
  fetchCourses,
  updateSearch,
  goToPreviousPage,
  goToNextPage,
} = useCourseCatalog();

const showFilters = ref(false);
const selectedCategory = ref<string>("all");
const selectedLevel = ref<string>("all");

const categories = [
  { value: "all", label: "Semua Kategori", icon: BookOpen },
  { value: "basic", label: "Dasar Investasi", icon: GraduationCap },
  { value: "stocks", label: "Saham Syariah", icon: TrendingUp },
  { value: "planning", label: "Perencanaan Keuangan", icon: Users },
];

const levels = [
  { value: "all", label: "Semua Level" },
  { value: "beginner", label: "Pemula" },
  { value: "intermediate", label: "Menengah" },
  { value: "advanced", label: "Lanjutan" },
];

function handleSearchInput(event: Event): void {
  const target = event.target as HTMLInputElement;
  updateSearch(target.value);
}

function toggleFilters(): void {
  showFilters.value = !showFilters.value;
}

function clearFilters(): void {
  selectedCategory.value = "all";
  selectedLevel.value = "all";
  updateSearch("");
}

const hasActiveFilters = () => {
  return (
    selectedCategory.value !== "all" ||
    selectedLevel.value !== "all" ||
    search.value
  );
};
</script>

<template>
  <GuestLayout>
    <main class="min-h-screen overflow-hidden bg-background text-neutral-high">
      <!-- Hero -->
      <section
        class="relative isolate overflow-hidden border-b border-primary-green/10 bg-gradient-to-br from-[#f3f8f4] via-[#f8faf6] to-white px-5 pb-16 pt-10 sm:px-8 sm:pb-20 sm:pt-14 lg:pb-24 lg:pt-16"
      >
        <div
          class="pointer-events-none absolute left-0 top-0 h-full w-1 bg-gradient-to-b from-primary-dark-green via-primary-green to-transparent"
        />

        <div
          class="pointer-events-none absolute -left-32 top-20 size-80 rounded-full bg-primary-green/5 blur-3xl"
        />

        <div
          class="pointer-events-none absolute -right-28 -top-16 size-96 rounded-full bg-lime-accent/10 blur-3xl"
        />

        <div
          class="pointer-events-none absolute inset-0 opacity-[0.025]"
          style="
            background-image: radial-gradient(
              circle at 1px 1px,
              #064e3b 1px,
              transparent 0
            );
            background-size: 30px 30px;
          "
        />

        <div class="relative mx-auto max-w-7xl">
          <div class="text-center">
            <div
              class="inline-flex items-center gap-2 rounded-full border border-primary-green/20 bg-white/80 px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-primary-green shadow-sm backdrop-blur-sm"
            >
              <Sparkles class="size-4" />
              Katalog Course
            </div>

            <h1
              class="mx-auto mt-6 max-w-4xl text-4xl font-bold leading-[1.12] tracking-tight text-primary-dark-green sm:text-5xl lg:text-[3.5rem]"
            >
              Jelajahi course investasi syariah
              <span class="relative inline-block text-primary-green">
                pilihan Anda
                <svg
                  class="absolute -bottom-1 left-0 h-3 w-full text-lime-accent"
                  viewBox="0 0 260 14"
                  fill="none"
                  aria-hidden="true"
                >
                  <path
                    d="M4 10C67 3 143 3 256 8"
                    stroke="currentColor"
                    stroke-width="6"
                    stroke-linecap="round"
                  />
                </svg>
              </span>
            </h1>

            <p
              class="mx-auto mt-6 max-w-2xl text-base leading-7 text-neutral-medium sm:text-lg"
            >
              Materi terstruktur, belajar bertahap, dan fokus praktis untuk
              membangun pemahaman investasi yang lebih baik.
            </p>

            <div class="mx-auto mt-10 max-w-3xl">
              <label
                class="relative block rounded-2xl border border-neutral-low bg-white p-2 shadow-[0_18px_50px_rgba(6,78,59,0.1)] transition focus-within:border-primary-green/50 focus-within:ring-4 focus-within:ring-primary-green/10"
              >
                <span class="sr-only">Cari course</span>

                <Search
                  class="pointer-events-none absolute left-6 top-1/2 size-5 -translate-y-1/2 text-primary-green"
                />

                <input
                  :value="search"
                  type="search"
                  maxlength="255"
                  placeholder="Cari judul atau topik course..."
                  class="w-full rounded-xl border-0 bg-transparent py-4 pl-12 pr-5 text-sm text-neutral-high outline-none placeholder:text-neutral-medium/70 sm:pr-40"
                  @input="handleSearchInput"
                />

                <a
                  href="#course-catalog"
                  class="absolute right-3 top-1/2 hidden -translate-y-1/2 items-center gap-2 rounded-xl bg-primary-dark-green px-5 py-3 text-sm font-bold text-white transition duration-300 hover:bg-primary-green sm:inline-flex"
                >
                  Cari Course
                  <ArrowRight class="size-4" />
                </a>
              </label>

              <div
                class="mt-4 flex flex-wrap items-center justify-center gap-3"
              >
                <span class="text-xs text-neutral-medium">Coba cari:</span>
                <button
                  type="button"
                  class="rounded-full border border-primary-green/20 bg-white px-3 py-1 text-xs font-medium text-primary-dark-green transition hover:border-primary-green/40 hover:bg-primary-green/5"
                  @click="updateSearch('saham syariah')"
                >
                  Saham Syariah
                </button>
                <button
                  type="button"
                  class="rounded-full border border-primary-green/20 bg-white px-3 py-1 text-xs font-medium text-primary-dark-green transition hover:border-primary-green/40 hover:bg-primary-green/5"
                  @click="updateSearch('perencanaan keuangan')"
                >
                  Perencanaan Keuangan
                </button>
                <button
                  type="button"
                  class="rounded-full border border-primary-green/20 bg-white px-3 py-1 text-xs font-medium text-primary-dark-green transition hover:border-primary-green/40 hover:bg-primary-green/5"
                  @click="updateSearch('zakat')"
                >
                  Zakat
                </button>
              </div>
            </div>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-6">
              <div
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-dark-green"
              >
                <CheckCircle2 class="size-5 text-primary-green" />
                Materi Terstruktur
              </div>

              <div
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-dark-green"
              >
                <Clock class="size-5 text-primary-green" />
                Belajar Fleksibel
              </div>

              <div
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-dark-green"
              >
                <GraduationCap class="size-5 text-primary-green" />
                Sertifikat Digital
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Course catalog -->
      <section
        id="course-catalog"
        class="relative px-5 pb-24 pt-12 sm:px-8 sm:pb-28 lg:pt-16"
        aria-labelledby="course-list-title"
      >
        <div
          class="pointer-events-none absolute -left-40 top-40 size-80 rounded-full bg-primary-green/[0.03] blur-3xl"
        />

        <div
          class="pointer-events-none absolute -right-40 bottom-20 size-80 rounded-full bg-lime-accent/[0.06] blur-3xl"
        />

        <div class="relative mx-auto max-w-7xl">
          <div
            class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
          >
            <div class="max-w-3xl">
              <div
                class="inline-flex items-center gap-3 text-xs font-bold uppercase tracking-[0.16em] text-primary-green"
              >
                <span class="h-0.5 w-8 bg-primary-green" />
                {{ courses.length }} Course Tersedia
              </div>

              <h2
                id="course-list-title"
                class="mt-3 text-2xl font-bold tracking-tight text-primary-dark-green sm:text-3xl"
              >
                Temukan course yang tepat
              </h2>
            </div>

            <div class="flex flex-wrap items-center gap-3">
              <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl border border-neutral-low bg-white px-4 py-2.5 text-sm font-medium text-neutral-high shadow-sm transition hover:border-primary-green hover:text-primary-green"
                :class="{
                  'border-primary-green bg-primary-green/5 text-primary-green':
                    showFilters,
                }"
                @click="toggleFilters"
              >
                <Filter class="size-4" />
                Filter
              </button>

              <button
                v-if="hasActiveFilters()"
                type="button"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-green/10 px-4 py-2.5 text-sm font-medium text-primary-dark-green transition hover:bg-primary-green/20"
                @click="clearFilters"
              >
                <X class="size-4" />
                Clear
              </button>
            </div>
          </div>

          <div
            v-if="showFilters"
            class="mb-8 rounded-2xl border border-neutral-low bg-white p-6 shadow-sm"
          >
            <div class="grid gap-6 sm:grid-cols-2">
              <div>
                <label
                  class="mb-3 block text-sm font-semibold text-neutral-high"
                >
                  Kategori
                </label>
                <div class="grid gap-2">
                  <button
                    v-for="category in categories"
                    :key="category.value"
                    type="button"
                    class="flex items-center gap-3 rounded-xl border border-neutral-low bg-white px-4 py-3 text-left text-sm font-medium text-neutral-high transition hover:border-primary-green hover:bg-primary-green/5"
                    :class="{
                      'border-primary-green bg-primary-green/5 text-primary-dark-green':
                        selectedCategory === category.value,
                    }"
                    @click="selectedCategory = category.value"
                  >
                    <component :is="category.icon" class="size-5 shrink-0" />
                    <span>{{ category.label }}</span>
                  </button>
                </div>
              </div>

              <div>
                <label
                  class="mb-3 block text-sm font-semibold text-neutral-high"
                >
                  Level
                </label>
                <div class="grid gap-2">
                  <button
                    v-for="level in levels"
                    :key="level.value"
                    type="button"
                    class="flex items-center gap-3 rounded-xl border border-neutral-low bg-white px-4 py-3 text-left text-sm font-medium text-neutral-high transition hover:border-primary-green hover:bg-primary-green/5"
                    :class="{
                      'border-primary-green bg-primary-green/5 text-primary-dark-green':
                        selectedLevel === level.value,
                    }"
                    @click="selectedLevel = level.value"
                  >
                    <span>{{ level.label }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading state -->
          <div
            v-if="isLoading"
            class="rounded-2xl border border-neutral-low bg-white p-12 shadow-sm"
          >
            <div class="grid min-h-80 place-items-center">
              <div class="text-center">
                <div
                  class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary-green/10"
                >
                  <LoaderCircle
                    class="size-8 animate-spin text-primary-green"
                  />
                </div>

                <p class="mt-5 font-semibold text-primary-dark-green">
                  Memuat daftar course
                </p>

                <p class="mt-2 text-sm text-neutral-medium">
                  Materi pembelajaran sedang disiapkan.
                </p>
              </div>
            </div>
          </div>

          <!-- Error state -->
          <div
            v-else-if="error"
            class="rounded-2xl border border-error/20 bg-white p-8 shadow-sm"
            role="alert"
          >
            <div class="grid min-h-80 place-items-center">
              <div class="max-w-md text-center">
                <div
                  class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-error/10"
                >
                  <CircleAlert class="size-8 text-error" />
                </div>

                <h3 class="mt-5 text-lg font-bold text-neutral-high">
                  Course belum dapat dimuat
                </h3>

                <p class="mt-3 text-sm leading-6 text-neutral-medium">
                  {{ error }}
                </p>

                <button
                  type="button"
                  class="mt-6 rounded-xl bg-primary-dark-green px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-green"
                  @click="fetchCourses"
                >
                  Coba Lagi
                </button>
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div
            v-else-if="courses.length === 0"
            class="rounded-2xl border border-dashed border-primary-green/30 bg-white p-8"
          >
            <div class="grid min-h-80 place-items-center">
              <div class="max-w-md text-center">
                <div
                  class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary-green/10"
                >
                  <BookOpen class="size-8 text-primary-green" />
                </div>

                <h3 class="mt-5 text-lg font-bold text-neutral-high">
                  Belum ada course yang ditemukan
                </h3>

                <p class="mt-3 text-sm leading-6 text-neutral-medium">
                  <span v-if="search">
                    Tidak ada course yang cocok dengan "{{ search }}".
                  </span>
                  <span v-else> Coba ubah filter atau pencarian Anda. </span>
                </p>

                <button
                  type="button"
                  class="mt-6 rounded-xl border border-primary-green bg-white px-6 py-2.5 text-sm font-semibold text-primary-dark-green transition hover:bg-primary-green/5"
                  @click="clearFilters"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          <!-- Course grid -->
          <div
            v-else
            class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
          >
            <CourseCatalogCard
              v-for="course in courses"
              :key="course.id"
              :course="course"
            />
          </div>

          <!-- Pagination -->
          <div
            v-if="!isLoading && !error && (canGoBack || canGoForward)"
            class="mt-12 flex flex-col items-center justify-center gap-6 border-t border-neutral-low pt-8 sm:flex-row"
          >
            <nav class="flex items-center gap-3" aria-label="Paginasi course">
              <button
                type="button"
                class="inline-flex size-10 items-center justify-center rounded-lg border border-neutral-low bg-white text-neutral-high shadow-sm transition hover:border-primary-green hover:text-primary-green disabled:cursor-not-allowed disabled:opacity-40"
                :disabled="!canGoBack"
                aria-label="Halaman sebelumnya"
                @click="goToPreviousPage"
              >
                <ChevronLeft class="size-5" />
              </button>

              <span
                class="inline-flex h-10 min-w-24 items-center justify-center rounded-lg bg-primary-dark-green px-4 text-sm font-bold text-white"
                :aria-label="`Halaman ${page}`"
              >
                {{ page }}
              </span>

              <button
                type="button"
                class="inline-flex size-10 items-center justify-center rounded-lg border border-neutral-low bg-white text-neutral-high shadow-sm transition hover:border-primary-green hover:text-primary-green disabled:cursor-not-allowed disabled:opacity-40"
                :disabled="!canGoForward"
                aria-label="Halaman berikutnya"
                @click="goToNextPage"
              >
                <ChevronRight class="size-5" />
              </button>
            </nav>

            <p class="text-sm text-neutral-medium">
              Menampilkan halaman {{ page }}
            </p>
          </div>
        </div>
      </section>
    </main>
  </GuestLayout>
</template>
