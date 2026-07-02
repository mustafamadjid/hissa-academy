<script setup lang="ts">
import {
  ArrowLeft,
  Award,
  BookOpen,
  CheckCircle2,
  Clock3,
  LoaderCircle,
  Play,
  ShieldCheck,
  ClipboardCheck,
  LockKeyhole,
} from "@lucide/vue";
import { computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useAuthStore } from "@/features/auth/stores/auth.store";
import GuestLayout from "@/layouts/Guest/GuestLayout.vue";

import CourseCurriculum from "../components/CourseCurriculum.vue";
import { useCourseDetail } from "../composables/useCourseDetail.ts";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const { course, isLoading, error, fetchCourse } = useCourseDetail();

const courseId = computed(() => String(route.params.courseId));
const firstAvailableLesson = computed(() =>
  course.value?.lessons
    .slice()
    .sort((left, right) => left.position - right.position)
    .find((lesson) => !lesson.is_locked),
);
const quizUnlocked = computed(() =>
  Boolean(course.value?.lessons.filter((lesson) => lesson.is_required).every(
    (lesson) => lesson.progress?.status === "completed",
  )),
);

async function startCourse(): Promise<void> {
  if (!authStore.isAuthenticated) {
    await router.push({
      name: "login-student",
      query: { redirect: route.fullPath },
    });
    return;
  }

  if (firstAvailableLesson.value) {
    await router.push({
      name: "student-lesson-detail",
      params: { lessonId: firstAvailableLesson.value.id },
    });
  }
}

onMounted(() => void fetchCourse(courseId.value, authStore.isAuthenticated));
</script>

<template>
  <GuestLayout>
    <main class="min-h-screen bg-background text-neutral-high">
      <div v-if="isLoading" class="grid min-h-[65vh] place-items-center">
        <div class="text-center">
          <LoaderCircle
            class="mx-auto size-9 animate-spin text-primary-green"
          />
          <p class="mt-4 text-sm text-neutral-medium">
            Memuat detail course...
          </p>
        </div>
      </div>

      <section
        v-else-if="error || !course"
        class="mx-auto max-w-3xl px-5 py-24 text-center"
      >
        <BookOpen class="mx-auto size-12 text-primary-green" />
        <h1 class="mt-5 text-2xl font-bold">Detail course tidak tersedia</h1>
        <p class="mt-3 text-neutral-medium">{{ error }}</p>
        <RouterLink
          :to="{ name: 'guest-courses' }"
          class="mt-7 inline-flex items-center gap-2 rounded-xl bg-primary-dark-green px-5 py-3 font-semibold text-white"
          ><ArrowLeft class="size-4" /> Kembali ke course</RouterLink
        >
      </section>

      <template v-else>
        <section
          class="relative overflow-hidden border-b border-primary-green/10 bg-gradient-to-br from-[#eef7f0] via-white to-[#f6fadf] px-5 py-14 sm:px-8 lg:py-20"
        >
          <div
            class="absolute -right-20 -top-20 size-80 rounded-full bg-lime-accent/15 blur-3xl"
          />
          <div
            class="relative mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-[1.15fr_0.85fr]"
          >
            <div>
              <RouterLink
                :to="{ name: 'guest-courses' }"
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-green"
                ><ArrowLeft class="size-4" /> Katalog Course</RouterLink
              >
              <p
                class="mt-7 inline-flex items-center gap-2 rounded-full bg-primary-green/10 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-primary-dark-green"
              >
                <BookOpen class="size-4" /> Course Online
              </p>
              <h1
                class="mt-5 max-w-3xl text-4xl font-bold leading-tight tracking-tight text-primary-dark-green sm:text-5xl"
              >
                {{ course.name }}
              </h1>
              <p class="mt-5 max-w-2xl text-base leading-7 text-neutral-medium">
                {{ course.description }}
              </p>
              <div class="mt-8 flex flex-wrap gap-3">
                <button
                  type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-primary-dark-green px-6 py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-primary-green disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="authStore.isAuthenticated && !firstAvailableLesson"
                  @click="startCourse"
                >
                  <Play class="size-4 fill-current" />
                  {{
                    authStore.isAuthenticated && course.progress_percentage > 0
                      ? "Lanjutkan Belajar"
                      : "Mulai Course"
                  }}
                </button>
                <span
                  class="inline-flex items-center gap-2 rounded-xl border border-neutral-low bg-white px-5 py-3 text-sm font-semibold text-neutral-high"
                  ><ShieldCheck class="size-4 text-primary-green" /> Materi
                  berurutan</span
                >
              </div>
            </div>

            <div
              class="relative min-h-72 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-dark-green via-primary-green to-emerald-accent p-8 shadow-elevation-3"
            >
              <div
                class="absolute -right-12 -top-12 size-48 rounded-full bg-lime-accent/20"
              />
              <div
                class="absolute -bottom-16 -left-12 size-56 rounded-full bg-white/10"
              />
              <div
                class="relative flex h-full min-h-56 flex-col justify-between text-white"
              >
                <BookOpen
                  class="size-20 text-lime-accent"
                  :stroke-width="1.2"
                />
                <div>
                  <p class="text-sm text-white/70">Progress course</p>
                  <div
                    class="mt-3 h-2.5 overflow-hidden rounded-full bg-white/20"
                  >
                    <div
                      class="h-full rounded-full bg-lime-accent"
                      :style="{ width: `${course.progress_percentage}%` }"
                    />
                  </div>
                  <p class="mt-2 text-sm font-bold">
                    {{ course.completed_lessons }} dari
                    {{ course.total_lessons }} lesson selesai
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section
          class="mx-auto grid max-w-7xl gap-10 px-5 py-12 sm:px-8 lg:grid-cols-[1fr_320px] lg:py-16"
        >
          <div class="space-y-12">
            <section>
              <p
                class="text-xs font-bold uppercase tracking-[0.16em] text-primary-green"
              >
                Tentang course
              </p>
              <h2 class="mt-2 text-2xl font-bold">Yang akan Anda pelajari</h2>
              <p
                class="mt-4 max-w-3xl whitespace-pre-line text-sm leading-7 text-neutral-medium"
              >
                {{ course.description }}
              </p>
            </section>
            <div
              class="rounded-2xl border border-primary-green/15 bg-white p-6 shadow-sm"
            >
              <div class="flex gap-4">
                <div
                  class="grid size-12 shrink-0 place-items-center rounded-xl bg-lime-accent/30"
                >
                  <Award class="size-6 text-primary-dark-green" />
                </div>
                <div>
                  <h2 class="font-bold">Selesaikan course secara bertahap</h2>
                  <p class="mt-2 text-sm leading-6 text-neutral-medium">
                    Setiap lesson terbuka setelah lesson wajib sebelumnya
                    selesai.
                  </p>
                </div>
              </div>
            </div>
            <CourseCurriculum
              :lessons="course.lessons"
              :authenticated="authStore.isAuthenticated"
              :course-id="course.id"
              :quiz-unlocked="quizUnlocked"
            />
            <section v-if="authStore.isAuthenticated" class="rounded-2xl border border-primary-green/15 bg-white p-6 shadow-sm">
              <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                <div class="grid size-12 shrink-0 place-items-center rounded-xl bg-primary-green/10">
                  <ClipboardCheck v-if="quizUnlocked" class="size-6 text-primary-green" />
                  <LockKeyhole v-else class="size-6 text-neutral-medium" />
                </div>
                <div class="flex-1"><h2 class="font-bold">Quiz akhir course</h2><p class="mt-1 text-sm text-neutral-medium">{{ quizUnlocked ? "Semua lesson wajib selesai. Quiz siap dikerjakan." : "Selesaikan semua lesson wajib untuk membuka quiz." }}</p></div>
                <RouterLink v-if="quizUnlocked" :to="{ name: 'student-course-quiz', params: { courseId: course.id } }" class="rounded-xl bg-primary-dark-green px-5 py-3 text-center text-sm font-bold text-white">Buka Quiz</RouterLink>
                <span v-else class="rounded-xl bg-surface-dim px-5 py-3 text-center text-sm font-semibold text-neutral-medium">Terkunci</span>
              </div>
            </section>
          </div>

          <aside
            class="h-fit rounded-2xl border border-neutral-low bg-white p-6 shadow-sm lg:sticky lg:top-28"
          >
            <h2 class="font-bold">Detail konten</h2>
            <ul class="mt-5 space-y-4 text-sm text-neutral-medium">
              <li class="flex items-center gap-3">
                <Clock3 class="size-5 text-primary-green" /> Belajar fleksibel
              </li>
              <li class="flex items-center gap-3">
                <BookOpen class="size-5 text-primary-green" />
                {{ course.total_lessons }} lesson
              </li>
              <li class="flex items-center gap-3">
                <CheckCircle2 class="size-5 text-primary-green" /> Nilai minimum
                {{ course.minimum_score }}
              </li>
              <li class="flex items-center gap-3">
                <Award class="size-5 text-primary-green" /> Sertifikat kelulusan
              </li>
            </ul>
          </aside>
        </section>
      </template>
    </main>
  </GuestLayout>
</template>
