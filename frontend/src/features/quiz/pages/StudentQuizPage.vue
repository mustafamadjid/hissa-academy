<script setup lang="ts">
import {
  ArrowLeft,
  Award,
  CheckCircle2,
  ClipboardCheck,
  LoaderCircle,
  LockKeyhole,
  RotateCcw,
  HelpCircle,
  Trophy,
  Target,
} from "@lucide/vue";
import { computed, watch } from "vue";
import { useRoute } from "vue-router";
import GuestLayout from "@/layouts/Guest/GuestLayout.vue";
import { useStudentQuiz } from "../composables/useStudentQuiz";

const route = useRoute();
const courseId = computed(() => String(route.params.courseId));
const {
  quiz,
  attempt,
  result,
  answers,
  isLoading,
  isSubmitting,
  error,
  isLocked,
  allAnswered,
  loadQuiz,
  startAttempt,
  selectAnswer,
  submitAttempt,
} = useStudentQuiz();

// Menghitung progress pengisian untuk progress bar sederhana
const progress = computed(() => {
  if (!attempt.value) return 0;
  const answered = Object.keys(answers.value).length;
  const total = attempt.value.questions.length;
  return (answered / total) * 100;
});

watch(courseId, (id) => void loadQuiz(id), { immediate: true });
</script>

<template>
  <GuestLayout>
    <main
      class="min-h-screen bg-[#F8FAFC] px-4 py-8 text-slate-900 sm:px-6 lg:py-12"
    >
      <div class="mx-auto max-w-3xl">
        <!-- Navigation -->
        <RouterLink
          :to="{ name: 'course-detail', params: { courseId } }"
          class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition-colors hover:text-primary-green"
        >
          <ArrowLeft
            class="size-4 transition-transform group-hover:-translate-x-1"
          />
          Kembali ke Dashboard Course
        </RouterLink>

        <!-- Loading State -->
        <div
          v-if="isLoading && !attempt"
          class="flex min-h-[50vh] flex-col items-center justify-center text-center"
        >
          <div class="relative flex items-center justify-center">
            <LoaderCircle class="size-12 animate-spin text-primary-green" />
            <div class="absolute size-8 rounded-full bg-primary-green/10"></div>
          </div>
          <p class="mt-4 font-medium text-slate-600">
            Menyiapkan materi quiz...
          </p>
        </div>

        <!-- Locked State -->
        <section
          v-else-if="(error && !quiz) || isLocked"
          class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
          <div class="bg-slate-50 p-8 text-center">
            <div
              class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-orange-100"
            >
              <LockKeyhole class="size-8 text-orange-600" />
            </div>
            <h1 class="mt-6 text-2xl font-bold text-slate-900">
              Quiz Belum Terbuka
            </h1>
            <p class="mx-auto mt-3 max-w-md text-slate-500">
              {{
                error ??
                "Selesaikan semua materi video dan tugas sebelum dapat mengakses evaluasi akhir ini."
              }}
            </p>
            <RouterLink
              :to="{ name: 'course-detail', params: { courseId } }"
              class="mt-8 inline-block rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50"
            >
              Lihat Progress Belajar
            </RouterLink>
          </div>
        </section>

        <!-- Result State -->
        <section
          v-else-if="result"
          class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
          <div class="p-8 text-center sm:p-12">
            <div
              class="mx-auto flex size-20 items-center justify-center rounded-full"
              :class="
                result.status === 'passed' ? 'bg-green-100' : 'bg-red-100'
              "
            >
              <Award
                v-if="result.status === 'passed'"
                class="size-10 text-green-600"
              />
              <RotateCcw v-else class="size-10 text-red-600" />
            </div>

            <div class="mt-6">
              <p
                class="text-sm font-bold uppercase tracking-[0.2em]"
                :class="
                  result.status === 'passed' ? 'text-green-600' : 'text-red-600'
                "
              >
                {{
                  result.status === "passed"
                    ? "Selamat! Anda Lulus"
                    : "Belum Mencapai Skor Minimum"
                }}
              </p>
              <h1 class="mt-2 text-6xl font-black text-slate-900">
                {{ result.score }}
              </h1>
            </div>

            <div
              class="mt-10 grid grid-cols-2 gap-4 rounded-2xl bg-slate-50 p-6"
            >
              <div class="text-center">
                <p class="text-xs font-medium uppercase text-slate-500">
                  Benar
                </p>
                <p class="mt-1 text-xl font-bold text-slate-900">
                  {{ result.result.correct_answers }} /
                  {{ result.result.total_questions }}
                </p>
              </div>
              <div class="border-l border-slate-200 text-center">
                <p class="text-xs font-medium uppercase text-slate-500">
                  Target
                </p>
                <p class="mt-1 text-xl font-bold text-slate-900">
                  {{ result.minimum_score }}
                </p>
              </div>
            </div>

            <div
              class="mt-10 flex flex-col gap-3 sm:flex-row sm:justify-center"
            >
              <button
                v-if="result.status === 'failed'"
                @click="loadQuiz(courseId)"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-dark-green px-8 py-4 font-bold text-white transition-transform hover:scale-[1.02] active:scale-[0.98]"
              >
                <RotateCcw class="size-5" /> Coba Lagi Sekarang
              </button>
              <RouterLink
                :to="{ name: 'course-detail', params: { courseId } }"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-8 py-4 font-bold text-white transition-transform hover:scale-[1.02] active:scale-[0.98]"
              >
                Kembali ke Materi
              </RouterLink>
            </div>
          </div>
        </section>

        <!-- Intro/Start State -->
        <template v-else-if="quiz">
          <section
            v-if="!attempt"
            class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          >
            <div class="border-b border-slate-100 bg-slate-50/50 p-8 sm:p-10">
              <div
                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-green text-white"
              >
                <ClipboardCheck class="size-8" />
              </div>
              <h1 class="mt-6 text-3xl font-extrabold text-slate-900">
                {{ quiz.name }}
              </h1>
              <p class="mt-3 text-lg text-slate-600 leading-relaxed">
                Uji pemahaman Anda mengenai materi ini untuk mendapatkan
                sertifikat kelulusan.
              </p>
            </div>

            <div class="p-8 sm:p-10">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="flex items-start gap-4">
                  <div class="mt-1 rounded-lg text-primary-dark-green">
                    <HelpCircle class="size-5" />
                  </div>
                  <div>
                    <p class="font-bold text-slate-900">
                      {{ quiz.total_questions }} Pertanyaan
                    </p>
                    <p class="text-sm text-slate-500">Pilihan ganda tunggal</p>
                  </div>
                </div>
                <div class="flex items-start gap-4">
                  <div class="mt-1 rounded-lg text-primary-dark-green">
                    <Target class="size-5" />
                  </div>
                  <div>
                    <p class="font-bold text-slate-900">
                      Skor Minimum {{ quiz.minimum_score }}
                    </p>
                    <p class="text-sm text-slate-500">
                      Syarat kelulusan course
                    </p>
                  </div>
                </div>
                <div class="flex items-start gap-4">
                  <div class="mt-1 rounded-lg text-primary-dark-green">
                    <Trophy class="size-5" />
                  </div>
                  <div>
                    <p class="font-bold text-slate-900">Percobaan</p>
                    <p class="text-sm text-slate-500">
                      Sudah dilakukan
                      {{ quiz.attempt_policy.attempts_used }} kali
                    </p>
                  </div>
                </div>
              </div>

              <div
                v-if="error"
                class="mt-8 rounded-xl bg-red-50 p-4 text-sm font-medium text-red-600 border border-red-100"
              >
                {{ error }}
              </div>

              <button
                type="button"
                class="mt-10 flex w-full items-center justify-center rounded-2xl bg-primary-green py-4 text-lg font-bold text-white transition-all hover:bg-primary-dark-green disabled:opacity-50"
                :disabled="isLoading || quiz.total_questions === 0"
                @click="startAttempt"
              >
                {{ isLoading ? "Menyiapkan Quiz..." : "Mulai Quiz Sekarang" }}
              </button>
            </div>
          </section>

          <!-- Quiz Form State -->
          <form v-else class="mt-8 space-y-8" @submit.prevent="submitAttempt">
            <!-- Floating Progress Bar -->
            <div
              class="sticky top-4 z-10 rounded-2xl border border-slate-200 bg-white/80 p-4 shadow-sm backdrop-blur-md"
            >
              <div
                class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500"
              >
                <span>Progress Pengerjaan</span>
                <span
                  >{{ Object.keys(answers).length }} /
                  {{ attempt.questions.length }}</span
                >
              </div>
              <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                <div
                  class="h-full bg-primary-green transition-all duration-300"
                  :style="{ width: `${progress}%` }"
                ></div>
              </div>
            </div>

            <div
              v-for="(question, index) in attempt.questions"
              :key="question.uuid"
              class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
            >
              <div class="flex gap-4">
                <span
                  class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-sm font-bold text-white"
                >
                  {{ index + 1 }}
                </span>
                <legend
                  class="text-lg font-bold leading-tight text-slate-900 sm:text-xl"
                >
                  {{ question.question_text }}
                </legend>
              </div>

              <div class="mt-8 space-y-3">
                <label
                  v-for="option in question.options"
                  :key="option.uuid"
                  class="group relative flex cursor-pointer items-center gap-4 rounded-xl border-2 p-4 transition-all"
                  :class="
                    answers[question.uuid] === option.uuid
                      ? 'border-primary-green bg-green-50/50 shadow-sm'
                      : 'border-slate-100 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <div
                    class="relative flex size-6 shrink-0 items-center justify-center rounded-full border-2 transition-colors"
                    :class="
                      answers[question.uuid] === option.uuid
                        ? 'border-primary-green bg-primary-green'
                        : 'border-slate-300'
                    "
                  >
                    <div
                      v-if="answers[question.uuid] === option.uuid"
                      class="size-2 rounded-full bg-white"
                    ></div>
                  </div>

                  <input
                    type="radio"
                    :name="question.uuid"
                    :value="option.uuid"
                    class="sr-only"
                    @change="selectAnswer(question.uuid, option.uuid)"
                  />

                  <span
                    class="text-base font-medium text-slate-700 transition-colors"
                    :class="{
                      'text-slate-900': answers[question.uuid] === option.uuid,
                    }"
                  >
                    {{ option.option_text }}
                  </span>

                  <CheckCircle2
                    v-if="answers[question.uuid] === option.uuid"
                    class="ml-auto size-5 text-primary-green"
                  />
                </label>
              </div>
            </div>

            <div class="pt-4">
              <div
                v-if="error"
                class="mb-6 rounded-xl bg-red-50 p-4 text-sm font-medium text-red-600 border border-red-100"
              >
                {{ error }}
              </div>

              <button
                type="submit"
                class="flex w-full items-center justify-center gap-3 rounded-2xl bg-slate-900 px-8 py-5 text-lg font-bold text-white shadow-lg transition-all hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-40 disabled:shadow-none"
                :disabled="!allAnswered || isSubmitting"
              >
                <template v-if="isSubmitting">
                  <LoaderCircle class="size-5 animate-spin" />
                  Mengirim Jawaban...
                </template>
                <template v-else> Selesaikan Quiz </template>
              </button>
              <p
                v-if="!allAnswered"
                class="mt-4 text-center text-sm font-medium text-slate-400"
              >
                Selesaikan semua pertanyaan untuk mengirim jawaban
              </p>
            </div>
          </form>
        </template>
      </div>
    </main>
  </GuestLayout>
</template>

<style scoped>
.transition-all {
  transition-duration: 200ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
