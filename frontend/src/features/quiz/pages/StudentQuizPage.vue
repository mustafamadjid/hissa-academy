<script setup lang="ts">
import { ArrowLeft, Award, CheckCircle2, ClipboardCheck, LoaderCircle, LockKeyhole, RotateCcw } from "@lucide/vue";
import { computed, watch } from "vue";
import { useRoute } from "vue-router";
import GuestLayout from "@/layouts/Guest/GuestLayout.vue";
import { useStudentQuiz } from "../composables/useStudentQuiz";

const route = useRoute();
const courseId = computed(() => String(route.params.courseId));
const { quiz, attempt, result, answers, isLoading, isSubmitting, error, isLocked, allAnswered, loadQuiz, startAttempt, selectAnswer, submitAttempt } = useStudentQuiz();

watch(courseId, (id) => void loadQuiz(id), { immediate: true });
</script>

<template>
  <GuestLayout>
    <main class="min-h-screen bg-background px-5 py-10 text-neutral-high sm:px-8">
      <div class="mx-auto max-w-4xl">
        <RouterLink :to="{ name: 'course-detail', params: { courseId } }" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-green">
          <ArrowLeft class="size-4" /> Kembali ke course
        </RouterLink>

        <div v-if="isLoading && !attempt" class="grid min-h-[60vh] place-items-center text-center">
          <div><LoaderCircle class="mx-auto size-10 animate-spin text-primary-green" /><p class="mt-4 text-neutral-medium">Memuat quiz...</p></div>
        </div>

        <section v-else-if="(error && !quiz) || isLocked" class="mt-12 rounded-3xl border border-neutral-low bg-white p-8 text-center shadow-sm">
          <LockKeyhole class="mx-auto size-12 text-primary-green" />
          <h1 class="mt-5 text-2xl font-bold">Quiz masih terkunci</h1>
          <p class="mx-auto mt-3 max-w-lg text-neutral-medium">{{ error ?? "Tuntaskan seluruh lesson wajib untuk membuka quiz course ini." }}</p>
        </section>

        <section v-else-if="result" class="mt-12 rounded-3xl border border-primary-green/20 bg-white p-8 text-center shadow-sm">
          <Award class="mx-auto size-14 text-primary-green" />
          <p class="mt-5 text-sm font-bold uppercase tracking-wider text-primary-green">Hasil Quiz</p>
          <h1 class="mt-2 text-4xl font-bold">{{ result.score }}</h1>
          <p class="mt-2 text-neutral-medium">Nilai minimum {{ result.minimum_score }}</p>
          <p class="mt-6 text-lg font-bold" :class="result.status === 'passed' ? 'text-primary-green' : 'text-red-700'">
            {{ result.status === "passed" ? "Lulus" : "Belum lulus" }}
          </p>
          <p class="mt-2 text-sm text-neutral-medium">{{ result.result.correct_answers }} benar dari {{ result.result.total_questions }} pertanyaan</p>
          <button v-if="result.status === 'failed'" type="button" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-primary-dark-green px-5 py-3 font-bold text-white" @click="loadQuiz(courseId)">
            <RotateCcw class="size-4" /> Coba Lagi
          </button>
        </section>

        <template v-else-if="quiz">
          <section v-if="!attempt" class="mt-12 overflow-hidden rounded-3xl bg-primary-dark-green p-8 text-white shadow-elevation-2 sm:p-12">
            <ClipboardCheck class="size-12 text-lime-accent" />
            <h1 class="mt-6 text-3xl font-bold">{{ quiz.name }}</h1>
            <p class="mt-3 text-white/75">Jawab seluruh pertanyaan dan raih nilai minimum {{ quiz.minimum_score }}.</p>
            <div class="mt-8 flex flex-wrap gap-5 text-sm"><span>{{ quiz.total_questions }} pertanyaan</span><span>{{ quiz.attempt_policy.attempts_used }} percobaan sebelumnya</span></div>
            <p v-if="error" role="alert" class="mt-6 rounded-xl bg-red-950/40 px-4 py-3 text-sm">{{ error }}</p>
            <button type="button" class="mt-8 rounded-xl bg-lime-accent px-6 py-3 font-bold text-primary-dark-green disabled:opacity-60" :disabled="isLoading || quiz.total_questions === 0" @click="startAttempt">
              {{ isLoading ? "Menyiapkan..." : "Mulai Quiz" }}
            </button>
          </section>

          <form v-else class="mt-10 space-y-6" @submit.prevent="submitAttempt">
            <header><p class="text-sm font-bold text-primary-green">{{ attempt.quiz.name }}</p><h1 class="mt-1 text-3xl font-bold">Pilih satu jawaban</h1></header>
            <fieldset v-for="(question, index) in attempt.questions" :key="question.uuid" class="rounded-2xl border border-neutral-low bg-white p-6 shadow-sm">
              <legend class="px-1 font-bold">{{ index + 1 }}. {{ question.question_text }}</legend>
              <div class="mt-5 space-y-3">
                <label v-for="option in question.options" :key="option.uuid" class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition" :class="answers[question.uuid] === option.uuid ? 'border-primary-green bg-primary-green/5' : 'border-neutral-low hover:border-primary-green/40'">
                  <input type="radio" :name="question.uuid" :value="option.uuid" :checked="answers[question.uuid] === option.uuid" class="accent-primary-green" @change="selectAnswer(question.uuid, option.uuid)" />
                  <span class="text-sm">{{ option.option_text }}</span>
                  <CheckCircle2 v-if="answers[question.uuid] === option.uuid" class="ml-auto size-5 text-primary-green" />
                </label>
              </div>
            </fieldset>
            <p v-if="error" role="alert" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ error }}</p>
            <button type="submit" class="w-full rounded-xl bg-primary-dark-green px-6 py-4 font-bold text-white disabled:cursor-not-allowed disabled:opacity-50" :disabled="!allAnswered || isSubmitting">
              {{ isSubmitting ? "Mengirim jawaban..." : "Kirim Jawaban" }}
            </button>
          </form>
        </template>
      </div>
    </main>
  </GuestLayout>
</template>
