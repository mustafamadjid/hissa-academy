```vue
<script setup lang="ts">
import {
  ArrowLeft,
  Award,
  CheckCircle2,
  ClipboardCheck,
  HelpCircle,
  LoaderCircle,
  LockKeyhole,
  RotateCcw,
  ShieldCheck,
  Target,
  Trophy,
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
  lockReason,
  allAnswered,
  loadQuiz,
  startAttempt,
  selectAnswer,
  submitAttempt,
} = useStudentQuiz();

const answeredCount = computed(() => Object.keys(answers.value).length);

const totalQuestions = computed(() => attempt.value?.questions.length ?? 0);

const progress = computed(() => {
  if (totalQuestions.value === 0) {
    return 0;
  }

  return (answeredCount.value / totalQuestions.value) * 100;
});

const isPassed = computed(() => result.value?.status === "passed");

function retryQuiz(): void {
  void loadQuiz(courseId.value);
}

watch(courseId, (id) => void loadQuiz(id), {
  immediate: true,
});
</script>

<template>
  <GuestLayout>
    <main class="min-h-screen bg-background text-neutral-high">
      <div class="mx-auto max-w-5xl px-5 py-10 sm:px-8 lg:py-16">
        <!-- Back navigation -->
        <RouterLink
          :to="{ name: 'course-detail', params: { courseId } }"
          class="group inline-flex items-center gap-2 text-sm font-semibold text-neutral-medium transition hover:text-primary-green"
        >
          <ArrowLeft
            class="size-4 transition-transform group-hover:-translate-x-1"
            aria-hidden="true"
          />

          <span>Kembali ke detail course</span>
        </RouterLink>

        <!-- Loading -->
        <section
          v-if="isLoading && !attempt"
          class="flex min-h-[60vh] flex-col items-center justify-center text-center"
        >
          <div
            class="flex size-16 items-center justify-center rounded-2xl bg-primary-green/10"
          >
            <LoaderCircle
              class="size-8 animate-spin text-primary-green"
              aria-hidden="true"
            />
          </div>

          <h1 class="mt-6 text-xl font-bold text-neutral-high">
            Menyiapkan quiz
          </h1>

          <p class="mt-2 text-sm text-neutral-medium">
            Data pertanyaan sedang dimuat.
          </p>
        </section>

        <!-- Locked -->
        <section
          v-else-if="(error && !quiz) || isLocked"
          class="mt-8 overflow-hidden rounded-3xl border border-neutral-low bg-surface shadow-sm"
        >
          <div class="px-6 py-12 text-center sm:px-10 sm:py-16">
            <div
              class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary-green/10 text-primary-green"
            >
              <LockKeyhole class="size-8" aria-hidden="true" />
            </div>

            <p
              class="mt-6 text-sm font-bold uppercase tracking-[0.16em] text-primary-green"
            >
              Akses quiz
            </p>

            <h1
              class="mt-2 text-2xl font-bold tracking-tight text-neutral-high sm:text-3xl"
            >
              {{
                lockReason === "QUIZ_ALREADY_PASSED"
                  ? "Quiz sudah diselesaikan"
                  : "Quiz belum dapat diakses"
              }}
            </h1>

            <p
              class="mx-auto mt-4 max-w-xl leading-7 text-neutral-medium"
            >
              {{
                error ??
                (lockReason === "QUIZ_ALREADY_PASSED"
                  ? "Anda sudah dinyatakan lulus pada quiz ini sehingga percobaan baru tidak diperlukan."
                  : "Selesaikan seluruh lesson wajib sebelum mengakses evaluasi akhir course.")
              }}
            </p>

            <RouterLink
              :to="{ name: 'course-detail', params: { courseId } }"
              class="mt-8 inline-flex h-12 items-center justify-center rounded-xl bg-primary-green px-6 text-sm font-semibold text-white transition hover:bg-primary-dark-green focus:outline-none focus:ring-4 focus:ring-primary-green/20"
            >
              Lihat progress belajar
            </RouterLink>
          </div>
        </section>

        <!-- Result -->
        <section
          v-else-if="result"
          class="mt-8 overflow-hidden rounded-3xl border border-neutral-low bg-surface shadow-sm"
        >
          <div
            :class="[
              'h-2 w-full',
              isPassed ? 'bg-primary-green' : 'bg-red-600',
            ]"
          />

          <div class="px-6 py-10 sm:px-10 sm:py-14">
            <div class="text-center">
              <div
                :class="[
                  'mx-auto flex size-20 items-center justify-center rounded-3xl',
                  isPassed
                    ? 'bg-primary-green/10 text-primary-green'
                    : 'bg-red-100 text-red-700',
                ]"
              >
                <Award
                  v-if="isPassed"
                  class="size-10"
                  aria-hidden="true"
                />

                <RotateCcw
                  v-else
                  class="size-10"
                  aria-hidden="true"
                />
              </div>

              <p
                :class="[
                  'mt-6 text-sm font-bold uppercase tracking-[0.18em]',
                  isPassed ? 'text-primary-green' : 'text-red-700',
                ]"
              >
                {{
                  isPassed
                    ? "Quiz berhasil diselesaikan"
                    : "Skor belum memenuhi target"
                }}
              </p>

              <h1
                class="mt-3 text-3xl font-bold tracking-tight text-neutral-high sm:text-4xl"
              >
                {{
                  isPassed
                    ? "Selamat, Anda dinyatakan lulus"
                    : "Silakan pelajari materi dan coba kembali"
                }}
              </h1>

              <div class="mt-8">
                <p class="text-sm font-semibold text-neutral-medium">
                  Skor akhir
                </p>

                <p class="mt-1 text-7xl font-black tracking-tight text-neutral-high">
                  {{ result.score }}
                </p>
              </div>
            </div>

            <div
              class="mx-auto mt-10 grid max-w-2xl gap-4 sm:grid-cols-3"
            >
              <div
                class="rounded-2xl border border-neutral-low bg-background p-5 text-center"
              >
                <p class="text-xs font-bold uppercase tracking-wider text-neutral-medium">
                  Jawaban benar
                </p>

                <p class="mt-2 text-xl font-bold text-neutral-high">
                  {{ result.result.correct_answers }}
                </p>
              </div>

              <div
                class="rounded-2xl border border-neutral-low bg-background p-5 text-center"
              >
                <p class="text-xs font-bold uppercase tracking-wider text-neutral-medium">
                  Total soal
                </p>

                <p class="mt-2 text-xl font-bold text-neutral-high">
                  {{ result.result.total_questions }}
                </p>
              </div>

              <div
                class="rounded-2xl border border-neutral-low bg-background p-5 text-center"
              >
                <p class="text-xs font-bold uppercase tracking-wider text-neutral-medium">
                  Skor minimum
                </p>

                <p class="mt-2 text-xl font-bold text-neutral-high">
                  {{ result.minimum_score }}
                </p>
              </div>
            </div>

            <div
              class="mt-10 flex flex-col justify-center gap-3 sm:flex-row"
            >
              <button
                v-if="!isPassed"
                type="button"
                class="inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-primary-green px-6 text-sm font-semibold text-white transition hover:bg-primary-dark-green focus:outline-none focus:ring-4 focus:ring-primary-green/20"
                @click="retryQuiz"
              >
                <RotateCcw class="size-4" aria-hidden="true" />
                Coba lagi
              </button>

              <RouterLink
                :to="{ name: 'course-detail', params: { courseId } }"
                class="inline-flex h-12 items-center justify-center rounded-xl border border-neutral-low bg-surface px-6 text-sm font-semibold text-neutral-high transition hover:border-primary-green/40 hover:bg-primary-green/5 hover:text-primary-dark-green"
              >
                Kembali ke materi
              </RouterLink>
            </div>
          </div>
        </section>

        <!-- Quiz available -->
        <template v-else-if="quiz">
          <!-- Intro -->
          <section
            v-if="!attempt"
            class="mt-8 overflow-hidden rounded-3xl border border-neutral-low bg-surface shadow-sm"
          >
            <div
              class="border-b border-neutral-low bg-primary-dark-green px-6 py-10 text-white sm:px-10 sm:py-12"
            >
              <div
                class="flex size-14 items-center justify-center rounded-2xl bg-white/10"
              >
                <ClipboardCheck class="size-7" aria-hidden="true" />
              </div>

              <p
                class="mt-8 text-sm font-bold uppercase tracking-[0.18em] text-white/65"
              >
                Evaluasi akhir
              </p>

              <h1
                class="mt-2 max-w-3xl text-3xl font-bold tracking-tight sm:text-4xl"
              >
                {{ quiz.name }}
              </h1>

              <p class="mt-4 max-w-2xl leading-7 text-white/75">
                Uji pemahaman terhadap materi course dan capai skor minimum
                untuk memperoleh kelulusan.
              </p>
            </div>

            <div class="px-6 py-8 sm:px-10 sm:py-10">
              <div class="grid gap-4 md:grid-cols-3">
                <div
                  class="rounded-2xl border border-neutral-low bg-background p-5"
                >
                  <div
                    class="flex size-10 items-center justify-center rounded-xl bg-primary-green/10 text-primary-green"
                  >
                    <HelpCircle class="size-5" aria-hidden="true" />
                  </div>

                  <p class="mt-4 font-bold text-neutral-high">
                    {{ quiz.total_questions }} pertanyaan
                  </p>

                  <p class="mt-1 text-sm leading-6 text-neutral-medium">
                    Setiap pertanyaan memiliki satu jawaban benar.
                  </p>
                </div>

                <div
                  class="rounded-2xl border border-neutral-low bg-background p-5"
                >
                  <div
                    class="flex size-10 items-center justify-center rounded-xl bg-primary-green/10 text-primary-green"
                  >
                    <Target class="size-5" aria-hidden="true" />
                  </div>

                  <p class="mt-4 font-bold text-neutral-high">
                    Skor minimum {{ quiz.minimum_score }}
                  </p>

                  <p class="mt-1 text-sm leading-6 text-neutral-medium">
                    Skor ini wajib dicapai untuk dinyatakan lulus.
                  </p>
                </div>

                <div
                  class="rounded-2xl border border-neutral-low bg-background p-5"
                >
                  <div
                    class="flex size-10 items-center justify-center rounded-xl bg-primary-green/10 text-primary-green"
                  >
                    <Trophy class="size-5" aria-hidden="true" />
                  </div>

                  <p class="mt-4 font-bold text-neutral-high">
                    {{ quiz.attempt_policy.attempts_used }} percobaan
                  </p>

                  <p class="mt-1 text-sm leading-6 text-neutral-medium">
                    Jumlah percobaan yang sudah digunakan.
                  </p>
                </div>
              </div>

              <div
                class="mt-6 flex items-start gap-3 rounded-2xl border border-primary-green/15 bg-primary-green/5 p-4"
              >
                <ShieldCheck
                  class="mt-0.5 size-5 shrink-0 text-primary-green"
                  aria-hidden="true"
                />

                <p class="text-sm leading-6 text-neutral-medium">
                  Pastikan koneksi stabil. Jawaban akan dikirim setelah seluruh
                  pertanyaan selesai diisi.
                </p>
              </div>

              <div
                v-if="error"
                class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700"
                role="alert"
              >
                {{ error }}
              </div>

              <button
                type="button"
                :disabled="isLoading || quiz.total_questions === 0"
                class="mt-8 inline-flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-primary-green px-6 text-base font-semibold text-white transition hover:bg-primary-dark-green focus:outline-none focus:ring-4 focus:ring-primary-green/20 disabled:cursor-not-allowed disabled:bg-neutral-low disabled:text-neutral-medium"
                @click="startAttempt"
              >
                <LoaderCircle
                  v-if="isLoading"
                  class="size-5 animate-spin"
                  aria-hidden="true"
                />

                <ClipboardCheck
                  v-else
                  class="size-5"
                  aria-hidden="true"
                />

                {{ isLoading ? "Menyiapkan quiz..." : "Mulai quiz" }}
              </button>
            </div>
          </section>

          <!-- Questions -->
          <form
            v-else
            class="mt-8 space-y-6"
            @submit.prevent="submitAttempt"
          >
            <!-- Progress -->
            <div
              class="sticky top-4 z-20 rounded-2xl border border-neutral-low bg-surface/95 p-5 shadow-sm backdrop-blur"
            >
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="text-sm font-bold text-neutral-high">
                    Progress pengerjaan
                  </p>

                  <p class="mt-1 text-xs text-neutral-medium">
                    {{ answeredCount }} dari {{ totalQuestions }} pertanyaan
                    telah dijawab
                  </p>
                </div>

                <div
                  class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary-green/10 text-sm font-bold text-primary-dark-green"
                >
                  {{ Math.round(progress) }}%
                </div>
              </div>

              <div
                class="mt-4 h-2 overflow-hidden rounded-full bg-neutral-low"
                role="progressbar"
                :aria-valuenow="Math.round(progress)"
                aria-valuemin="0"
                aria-valuemax="100"
              >
                <div
                  class="h-full rounded-full bg-primary-green transition-[width] duration-300"
                  :style="{ width: `${progress}%` }"
                />
              </div>
            </div>

            <!-- Question cards -->
            <fieldset
              v-for="(question, index) in attempt.questions"
              :key="question.uuid"
              class="rounded-3xl border border-neutral-low bg-surface p-6 shadow-sm sm:p-8"
            >
              <div class="flex items-start gap-4">
                <div
                  class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-dark-green text-sm font-bold text-white"
                >
                  {{ index + 1 }}
                </div>

                <div class="min-w-0">
                  <p
                    class="text-xs font-bold uppercase tracking-[0.14em] text-primary-green"
                  >
                    Pertanyaan {{ index + 1 }}
                  </p>

                  <legend
                    class="mt-2 text-lg font-bold leading-7 text-neutral-high sm:text-xl"
                  >
                    {{ question.question_text }}
                  </legend>
                </div>
              </div>

              <div class="mt-7 space-y-3">
                <label
                  v-for="(option, optionIndex) in question.options"
                  :key="option.uuid"
                  :class="[
                    'group flex cursor-pointer items-center gap-4 rounded-2xl border p-4 transition sm:p-5',
                    answers[question.uuid] === option.uuid
                      ? 'border-primary-green bg-primary-green/5 ring-1 ring-primary-green'
                      : 'border-neutral-low bg-surface hover:border-primary-green/40 hover:bg-primary-green/[0.03]',
                  ]"
                >
                  <input
                    type="radio"
                    :name="question.uuid"
                    :value="option.uuid"
                    :checked="answers[question.uuid] === option.uuid"
                    class="sr-only"
                    @change="selectAnswer(question.uuid, option.uuid)"
                  />

                  <span
                    :class="[
                      'flex size-9 shrink-0 items-center justify-center rounded-xl border text-sm font-bold transition',
                      answers[question.uuid] === option.uuid
                        ? 'border-primary-green bg-primary-green text-white'
                        : 'border-neutral-low bg-background text-neutral-medium group-hover:border-primary-green/40 group-hover:text-primary-green',
                    ]"
                  >
                    {{ String.fromCharCode(65 + optionIndex) }}
                  </span>

                  <span
                    :class="[
                      'flex-1 text-sm font-medium leading-6 sm:text-base',
                      answers[question.uuid] === option.uuid
                        ? 'text-neutral-high'
                        : 'text-neutral-medium',
                    ]"
                  >
                    {{ option.option_text }}
                  </span>

                  <CheckCircle2
                    v-if="answers[question.uuid] === option.uuid"
                    class="size-5 shrink-0 text-primary-green"
                    aria-hidden="true"
                  />
                </label>
              </div>
            </fieldset>

            <!-- Submit -->
            <section
              class="rounded-3xl border border-neutral-low bg-surface p-6 shadow-sm sm:p-8"
            >
              <div
                v-if="error"
                class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700"
                role="alert"
              >
                {{ error }}
              </div>

              <div
                class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
              >
                <div>
                  <p class="font-bold text-neutral-high">
                    Siap mengirim jawaban?
                  </p>

                  <p class="mt-1 text-sm leading-6 text-neutral-medium">
                    Pastikan semua jawaban sudah diperiksa sebelum dikirim.
                  </p>
                </div>

                <button
                  type="submit"
                  :disabled="!allAnswered || isSubmitting"
                  class="inline-flex h-13 shrink-0 items-center justify-center gap-2 rounded-xl bg-primary-green px-7 text-sm font-semibold text-white transition hover:bg-primary-dark-green focus:outline-none focus:ring-4 focus:ring-primary-green/20 disabled:cursor-not-allowed disabled:bg-neutral-low disabled:text-neutral-medium"
                >
                  <LoaderCircle
                    v-if="isSubmitting"
                    class="size-5 animate-spin"
                    aria-hidden="true"
                  />

                  <CheckCircle2
                    v-else
                    class="size-5"
                    aria-hidden="true"
                  />

                  {{
                    isSubmitting
                      ? "Mengirim jawaban..."
                      : "Selesaikan quiz"
                  }}
                </button>
              </div>

              <p
                v-if="!allAnswered"
                class="mt-5 text-sm font-medium text-neutral-medium sm:text-right"
              >
                Jawab seluruh pertanyaan untuk mengaktifkan tombol.
              </p>
            </section>
          </form>
        </template>
      </div>
    </main>
  </GuestLayout>
</template>
```
