<script setup lang="ts">
import {
  ArrowRight,
  BookOpen,
  CheckCircle2,
  ChevronLeft,
  ChevronRight,
  CircleAlert,
  GraduationCap,
  LoaderCircle,
  Search,
  Sparkles,
} from "@lucide/vue";

import heroImage from "@/assets/images/landing-page/landing-3.png";
import GuestLayout from "@/layouts/Guest/GuestLayout.vue";

import CourseCatalogCard from "../components/CourseCatalogCard.vue";
import { useCourseCatalog } from "../composables/useCourseCatalog";

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

function handleSearchInput(event: Event): void {
  const target = event.target as HTMLInputElement;

  updateSearch(target.value);
}
</script>

<template>
  <GuestLayout>
    <main class="min-h-screen overflow-hidden bg-background text-neutral-high">
      <!-- Hero -->
      <section
        class="relative isolate overflow-hidden border-b border-primary-green/10 bg-[#f3f8f4] px-5 pb-20 pt-12 sm:px-8 sm:pb-24 sm:pt-16 lg:pb-28 lg:pt-20"
      >
        <!-- Background decorations -->
        <div
          class="pointer-events-none absolute left-0 top-0 h-full w-2 bg-primary-dark-green"
        />

        <div
          class="pointer-events-none absolute -left-32 top-20 size-80 rounded-full bg-primary-green/5"
        />

        <div
          class="pointer-events-none absolute -right-28 -top-16 size-96 rounded-full bg-lime-accent/10"
        />

        <div
          class="pointer-events-none absolute inset-0 opacity-[0.035]"
          style="
            background-image: radial-gradient(
              circle at 1px 1px,
              #064e3b 1px,
              transparent 0
            );
            background-size: 30px 30px;
          "
        />

        <div
          class="relative mx-auto grid max-w-7xl items-center gap-14 lg:grid-cols-[1.05fr_0.95fr] lg:gap-20"
        >
          <!-- Hero content -->
          <div class="relative z-10">
            <div
              class="inline-flex items-center gap-2 rounded-full border border-primary-green/20 bg-white px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-primary-green shadow-sm"
            >
              <Sparkles class="size-4" />
              Katalog HISSA Academy
            </div>

            <h1
              class="mt-7 max-w-3xl text-4xl font-bold leading-[1.12] tracking-tight text-primary-dark-green sm:text-5xl lg:text-[3.75rem]"
            >
              Belajar investasi syariah dengan jalur yang
              <span
                class="relative inline-block text-primary-green sm:whitespace-nowrap"
              >
                lebih terarah

                <svg
                  class="absolute -bottom-2 left-0 h-3 w-full text-lime-accent"
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
              class="mt-8 max-w-2xl text-base leading-8 text-neutral-medium sm:text-lg"
            >
              Temukan course yang dirancang secara sistematis untuk membantu
              Anda memahami investasi syariah, mulai dari prinsip dasar hingga
              penerapan dalam pengambilan keputusan.
            </p>

            <!-- Benefits -->
            <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3">
              <div
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-dark-green"
              >
                <CheckCircle2 class="size-5 text-primary-green" />
                Materi terstruktur
              </div>

              <div
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-dark-green"
              >
                <CheckCircle2 class="size-5 text-primary-green" />
                Belajar bertahap
              </div>

              <div
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-dark-green"
              >
                <CheckCircle2 class="size-5 text-primary-green" />
                Fokus praktis
              </div>
            </div>

            <!-- Hero search -->
            <label
              class="relative mt-10 block max-w-2xl rounded-2xl border border-neutral-low bg-white p-2 shadow-[0_18px_50px_rgba(6,78,59,0.1)] transition focus-within:border-primary-green/50 focus-within:ring-4 focus-within:ring-primary-green/10"
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

            <p class="mt-4 text-xs leading-5 text-neutral-medium">
              Contoh pencarian: saham syariah, perencanaan keuangan, atau zakat.
            </p>
          </div>

          <!-- Hero visual -->
          <div class="relative mx-auto w-full max-w-xl lg:max-w-none">
            <div
              class="absolute inset-x-10 bottom-3 top-12 rotate-3 rounded-[2.75rem] bg-primary-green/10"
            />

            <div
              class="absolute inset-x-16 bottom-10 top-4 -rotate-3 rounded-[2.75rem] border border-primary-green/15 bg-white/50"
            />

            <div
              class="relative overflow-hidden rounded-[2.5rem] border border-primary-green/10 bg-white p-6 shadow-[0_30px_80px_rgba(6,78,59,0.14)] sm:p-8"
            >
              <!-- Solid accent -->
              <div class="absolute left-0 top-0 h-2 w-full bg-primary-green" />

              <div
                class="absolute bottom-8 left-8 size-36 rounded-full bg-primary-green/5"
              />

              <!-- Floating top card -->
              <div
                class="absolute left-5 top-5 z-20 rounded-2xl border border-neutral-low bg-white p-3 shadow-lg sm:left-7 sm:top-7"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="flex size-10 items-center justify-center rounded-xl bg-primary-green/10 text-primary-green"
                  >
                    <GraduationCap class="size-5" />
                  </div>

                  <div>
                    <p class="text-sm font-bold text-primary-dark-green">
                      Kurikulum Terarah
                    </p>
                    <p class="mt-0.5 text-xs text-neutral-medium">
                      Belajar sesuai urutan
                    </p>
                  </div>
                </div>
              </div>

              <img
                :src="heroImage"
                alt="Ilustrasi pembelajaran investasi di HISSA Academy"
                fetchpriority="high"
                decoding="async"
                class="relative z-10 h-80 w-full object-contain object-bottom pt-12 sm:h-[430px] lg:h-[470px]"
              />

              <!-- Floating bottom card -->
              <div
                class="absolute bottom-5 right-5 z-20 max-w-52 rounded-2xl border border-neutral-low bg-white p-4 shadow-xl sm:bottom-7 sm:right-7"
              >
                <div class="flex items-start gap-3">
                  <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-lime-accent text-primary-dark-green"
                  >
                    <BookOpen class="size-5" />
                  </div>

                  <div>
                    <p class="text-sm font-bold text-primary-dark-green">
                      Belajar Bertahap
                    </p>
                    <p class="mt-1 text-xs leading-5 text-neutral-medium">
                      Bangun pemahaman dari konsep dasar hingga penerapan.
                    </p>
                  </div>
                </div>
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
        <!-- Solid decorative shapes -->
        <div
          class="pointer-events-none absolute -left-40 top-40 size-80 rounded-full bg-primary-green/[0.03]"
        />

        <div
          class="pointer-events-none absolute -right-40 bottom-20 size-80 rounded-full bg-lime-accent/[0.06]"
        />

        <div class="relative mx-auto max-w-7xl">
          <!-- Catalog heading -->
          <div
            class="mb-10 flex flex-col gap-6 border-b border-neutral-low pb-8 lg:flex-row lg:items-end lg:justify-between"
          >
            <div class="max-w-3xl">
              <div
                class="inline-flex items-center gap-3 text-xs font-bold uppercase tracking-[0.16em] text-primary-green"
              >
                <span class="h-0.5 w-8 bg-primary-green" />
                Course tersedia
              </div>

              <h2
                id="course-list-title"
                class="mt-4 text-3xl font-bold tracking-tight text-primary-dark-green sm:text-4xl"
              >
                Pilih course sesuai target belajar Anda
              </h2>

              <p class="mt-4 max-w-2xl leading-7 text-neutral-medium">
                Setiap course dirancang untuk memberikan proses belajar yang
                terarah, relevan, dan mudah diikuti.
              </p>
            </div>

            <!-- Pagination desktop -->
            <nav
              v-if="!isLoading && !error && (canGoBack || canGoForward)"
              class="hidden shrink-0 items-center gap-2 sm:flex"
              aria-label="Paginasi course"
            >
              <button
                type="button"
                class="inline-flex size-11 items-center justify-center rounded-xl border border-neutral-low bg-white text-neutral-high shadow-sm transition hover:border-primary-green hover:text-primary-green disabled:cursor-not-allowed disabled:opacity-40"
                :disabled="!canGoBack"
                aria-label="Halaman sebelumnya"
                @click="goToPreviousPage"
              >
                <ChevronLeft class="size-5" />
              </button>

              <span
                class="inline-flex h-11 min-w-28 items-center justify-center rounded-xl bg-primary-dark-green px-4 text-sm font-bold text-white shadow-lg shadow-primary-dark-green/15"
                :aria-label="`Halaman ${page}`"
              >
                Halaman {{ page }}
              </span>

              <button
                type="button"
                class="inline-flex size-11 items-center justify-center rounded-xl border border-neutral-low bg-white text-neutral-high shadow-sm transition hover:border-primary-green hover:text-primary-green disabled:cursor-not-allowed disabled:opacity-40"
                :disabled="!canGoForward"
                aria-label="Halaman berikutnya"
                @click="goToNextPage"
              >
                <ChevronRight class="size-5" />
              </button>
            </nav>
          </div>

          <!-- Loading state -->
          <div
            v-if="isLoading"
            class="grid min-h-[420px] place-items-center rounded-3xl border border-neutral-low bg-white shadow-sm"
            role="status"
            aria-live="polite"
          >
            <div class="text-center">
              <div
                class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary-green/10"
              >
                <LoaderCircle class="size-8 animate-spin text-primary-green" />
              </div>

              <p class="mt-5 font-bold text-primary-dark-green">
                Memuat daftar course
              </p>

              <p class="mt-2 text-sm text-neutral-medium">
                Materi pembelajaran sedang disiapkan.
              </p>
            </div>
          </div>

          <!-- Error state -->
          <div
            v-else-if="error"
            class="grid min-h-[420px] place-items-center rounded-3xl border border-error/20 bg-white p-8 shadow-sm"
            role="alert"
          >
            <div class="max-w-md text-center">
              <div
                class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-error/10"
              >
                <CircleAlert class="size-8 text-error" />
              </div>

              <h3 class="mt-5 text-xl font-bold text-neutral-high">
                Course belum dapat dimuat
              </h3>

              <p class="mt-3 text-sm leading-6 text-neutral-medium">
                {{ error }}
              </p>

              <button
                type="button"
                class="mt-6 rounded-xl bg-primary-dark-green px-6 py-3 text-sm font-semibold text-white transition hover:bg-primary-green"
                @click="fetchCourses"
              >
                Coba lagi
              </button>
            </div>
          </div>

          <!-- Empty state -->
          <div
            v-else-if="courses.length === 0"
            class="grid min-h-[420px] place-items-center rounded-3xl border border-dashed border-primary-green/30 bg-white p-8"
          >
            <div class="max-w-md text-center">
              <div
                class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary-green/10"
              >
                <BookOpen class="size-8 text-primary-green" />
              </div>

              <h3 class="mt-5 text-xl font-bold text-neutral-high">
                Course tidak ditemukan
              </h3>

              <p class="mt-3 text-sm leading-6 text-neutral-medium">
                Tidak ada course yang cocok dengan pencarian
                <span v-if="search" class="font-semibold text-neutral-high">
                  “{{ search }}”
                </span>
                .
              </p>
            </div>
          </div>

          <!-- Course grid -->
          <div
            v-else
            class="grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3"
          >
            <CourseCatalogCard
              v-for="course in courses"
              :key="course.id"
              :course="course"
            />
          </div>

          <!-- Pagination mobile -->
          <nav
            v-if="!isLoading && !error && (canGoBack || canGoForward)"
            class="mt-10 flex items-center justify-center gap-2 sm:hidden"
            aria-label="Paginasi course mobile"
          >
            <button
              type="button"
              class="inline-flex size-11 items-center justify-center rounded-xl border border-neutral-low bg-white text-neutral-high disabled:cursor-not-allowed disabled:opacity-40"
              :disabled="!canGoBack"
              aria-label="Halaman sebelumnya"
              @click="goToPreviousPage"
            >
              <ChevronLeft class="size-5" />
            </button>

            <span
              class="inline-flex h-11 min-w-28 items-center justify-center rounded-xl bg-primary-dark-green px-4 text-sm font-bold text-white"
              :aria-label="`Halaman ${page}`"
            >
              Halaman {{ page }}
            </span>

            <button
              type="button"
              class="inline-flex size-11 items-center justify-center rounded-xl border border-neutral-low bg-white text-neutral-high disabled:cursor-not-allowed disabled:opacity-40"
              :disabled="!canGoForward"
              aria-label="Halaman berikutnya"
              @click="goToNextPage"
            >
              <ChevronRight class="size-5" />
            </button>
          </nav>
        </div>
      </section>
    </main>
  </GuestLayout>
</template>
