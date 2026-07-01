<script setup lang="ts">
import { computed, ref, watch } from "vue";
import {
  AlertCircle,
  ArrowLeft,
  CheckCircle2,
  CircleHelp,
  Pencil,
  Plus,
  RotateCcw,
  Trash2,
} from "@lucide/vue";
import { useRoute } from "vue-router";

import AdminDashboard from "@/layouts/AdminDashboard/AdminDashboard.vue";
import DeleteConfirmationModal from "@/shared/components/ui/DeleteConfirmationModal.vue";
import QuizFormModal from "../../components/QuizFormModal.vue";
import QuizQuestionDraggableList from "../../components/QuizQuestionDraggableList.vue";
import QuizQuestionFormModal from "../../components/QuizQuestionFormModal.vue";
import { useAdminCourseQuiz } from "../../composables/useAdminCourseQuiz";
import type {
  AdminQuizQuestionDto,
  QuestionFormValues,
  QuizFormValues,
} from "../../types/admin-quiz.types";

const route = useRoute();
const courseId = computed(() =>
  typeof route.params.courseId === "string" ? route.params.courseId : "",
);
const selectedQuestion = ref<AdminQuizQuestionDto | null>(null);
const isQuizFormOpen = ref(false);
const isQuizDeleteOpen = ref(false);
const isQuestionFormOpen = ref(false);
const isQuestionDeleteOpen = ref(false);

const {
  quiz,
  questions,
  isLoading,
  hasLoadError,
  isSavingQuiz,
  isDeletingQuiz,
  isSavingQuestion,
  isReordering,
  deletingQuestionId,
  errorMessage,
  successMessage,
  fetchPage,
  saveQuiz,
  removeQuiz,
  createQuestion,
  saveQuestion,
  removeQuestion,
  reorderQuestions,
  clearMessages,
} = useAdminCourseQuiz();

const isQuestionListDisabled = computed(
  () =>
    isSavingQuestion.value ||
    isReordering.value ||
    deletingQuestionId.value !== null ||
    isDeletingQuiz.value,
);

function openCreateQuiz(): void {
  if (quiz.value) return;
  clearMessages();
  isQuizFormOpen.value = true;
}

function openEditQuiz(): void {
  if (!quiz.value) return;
  clearMessages();
  isQuizFormOpen.value = true;
}

function openDeleteQuiz(): void {
  clearMessages();
  isQuizDeleteOpen.value = true;
}

async function submitQuiz(values: QuizFormValues): Promise<void> {
  if (!courseId.value) return;

  const saved = await saveQuiz(courseId.value, values);
  if (saved) isQuizFormOpen.value = false;
}

async function confirmDeleteQuiz(): Promise<void> {
  const deleted = await removeQuiz();
  if (deleted) isQuizDeleteOpen.value = false;
}

function openCreateQuestion(): void {
  clearMessages();
  selectedQuestion.value = null;
  isQuestionFormOpen.value = true;
}

function openEditQuestion(question: AdminQuizQuestionDto): void {
  clearMessages();
  selectedQuestion.value = question;
  isQuestionFormOpen.value = true;
}

function openDeleteQuestion(question: AdminQuizQuestionDto): void {
  clearMessages();
  selectedQuestion.value = question;
  isQuestionDeleteOpen.value = true;
}

async function submitQuestion(values: QuestionFormValues): Promise<void> {
  const saved = selectedQuestion.value
    ? await saveQuestion(selectedQuestion.value, values)
    : await createQuestion(values);

  if (saved) {
    isQuestionFormOpen.value = false;
    selectedQuestion.value = null;
  }
}

async function confirmDeleteQuestion(): Promise<void> {
  if (!selectedQuestion.value) return;

  const deleted = await removeQuestion(selectedQuestion.value.id);
  if (deleted) {
    isQuestionDeleteOpen.value = false;
    selectedQuestion.value = null;
  }
}

function retryFetch(): void {
  if (courseId.value) void fetchPage(courseId.value);
}

watch(
  courseId,
  (id) => {
    if (id) void fetchPage(id);
  },
  { immediate: true },
);
</script>

<template>
  <AdminDashboard>
    <main class="mx-auto w-full max-w-350 px-4 py-6 md:px-6 md:py-8">
      <nav aria-label="Breadcrumb" class="mb-5 flex items-center gap-3 text-xs">
        <RouterLink
          :to="{ name: 'admin-course-detail', params: { courseId } }"
          class="inline-flex items-center gap-1.5 font-medium text-slate-500 transition-colors hover:text-emerald-700 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-emerald-600"
        >
          <ArrowLeft :size="14" aria-hidden="true" />
          Kembali ke Detail Course
        </RouterLink>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <span class="font-semibold text-slate-700">Kelola Quiz</span>
      </nav>

      <div
        v-if="successMessage"
        role="status"
        class="mb-5 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800"
      >
        <CheckCircle2 :size="18" class="mt-0.5 shrink-0" aria-hidden="true" />
        <span class="text-sm">{{ successMessage }}</span>
      </div>

      <div
        v-if="errorMessage && !hasLoadError"
        role="alert"
        class="mb-5 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800"
      >
        <AlertCircle :size="18" class="mt-0.5 shrink-0" aria-hidden="true" />
        <span class="text-sm">{{ errorMessage }}</span>
      </div>

      <div
        v-if="isLoading"
        class="space-y-6"
        role="status"
        aria-label="Memuat data quiz"
      >
        <USkeleton class="h-40 w-full rounded-xl" />
        <USkeleton class="h-52 w-full rounded-xl" />
      </div>

      <div
        v-else-if="hasLoadError"
        class="flex min-h-80 flex-col items-center justify-center rounded-xl border border-red-200 bg-white px-6 py-12 text-center"
      >
        <span
          class="mb-4 flex size-12 items-center justify-center rounded-full bg-red-50 text-red-600"
        >
          <AlertCircle :size="23" aria-hidden="true" />
        </span>
        <h1 class="text-lg font-bold text-slate-900">
          Data quiz tidak tersedia
        </h1>
        <p class="mt-2 max-w-md text-sm leading-6 text-slate-500">
          {{ errorMessage }}
        </p>
        <UButton
          color="neutral"
          variant="outline"
          class="mt-5"
          @click="retryFetch"
        >
          <RotateCcw :size="16" aria-hidden="true" />
          Muat ulang
        </UButton>
      </div>

      <div v-else class="space-y-7">
        <header
          class="relative overflow-hidden rounded-xl bg-emerald-950 px-5 py-7 text-white shadow-sm sm:px-7"
        >
          <div
            class="absolute -right-12 -top-20 size-56 rounded-full bg-emerald-700/40"
            aria-hidden="true"
          />
          <div
            class="relative flex flex-wrap items-start justify-between gap-5"
          >
            <div class="flex items-start gap-4">
              <span
                class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-white/10 text-emerald-100"
              >
                <CircleHelp :size="24" aria-hidden="true" />
              </span>
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wider text-emerald-300"
                >
                  Kelola Quiz
                </p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                  {{ quiz?.quiz_name ?? "Quiz belum dibuat" }}
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-emerald-100/75">
                  Kelola nama quiz, pertanyaan, opsi jawaban, kunci jawaban, dan
                  urutannya.
                </p>
              </div>
            </div>

            <UButton
              color="primary"
              variant="solid"
              class="w-full justify-center bg-white text-emerald-900 hover:bg-emerald-50 sm:w-auto"
              :disabled="Boolean(quiz) || isSavingQuiz"
              @click="openCreateQuiz"
            >
              <Plus :size="17" aria-hidden="true" />
              Tambah Quiz
            </UButton>
          </div>
        </header>

        <section
          v-if="!quiz"
          class="flex flex-col items-center rounded-xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center"
        >
          <span
            class="mb-3 flex size-11 items-center justify-center rounded-full bg-slate-100 text-slate-400"
          >
            <CircleHelp :size="21" aria-hidden="true" />
          </span>
          <h2 class="text-sm font-semibold text-slate-800">Belum ada quiz</h2>
          <p class="mt-1 max-w-md text-xs leading-5 text-slate-500">
            Tambahkan quiz terlebih dahulu sebelum membuat pertanyaan.
          </p>
          <UButton class="mt-5" @click="openCreateQuiz">
            <Plus :size="16" aria-hidden="true" />
            Tambah Quiz
          </UButton>
        </section>

        <template v-else>
          <section
            class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
          >
            <div class="flex flex-wrap items-center justify-between gap-4">
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                >
                  Nama Quiz
                </p>
                <h2 class="mt-1 text-xl font-bold text-emerald-950">
                  {{ quiz.quiz_name }}
                </h2>
              </div>
              <div class="flex flex-wrap gap-2">
                <UButton
                  class="p-3 cursor-pointer"
                  color="neutral"
                  variant="outline"
                  :disabled="isQuestionListDisabled"
                  @click="openEditQuiz"
                >
                  <Pencil :size="16" aria-hidden="true" />
                  Edit Quiz
                </UButton>
                <UButton
                  class="p-3 cursor-pointer"
                  color="error"
                  variant="outline"
                  :disabled="isQuestionListDisabled"
                  @click="openDeleteQuiz"
                >
                  <Trash2 :size="16" aria-hidden="true" />
                  Hapus Quiz
                </UButton>
                <UButton
                  class="p-3 cursor-pointer"
                  :disabled="isQuestionListDisabled"
                  @click="openCreateQuestion"
                >
                  <Plus :size="16" aria-hidden="true" />
                  Tambah Pertanyaan
                </UButton>
              </div>
            </div>
          </section>

          <QuizQuestionDraggableList
            :questions="questions"
            :disabled="isQuestionListDisabled"
            @edit="openEditQuestion"
            @delete="openDeleteQuestion"
            @reorder="reorderQuestions"
          />
        </template>
      </div>
    </main>

    <QuizFormModal
      v-model:open="isQuizFormOpen"
      :quiz="quiz"
      :is-submitting="isSavingQuiz"
      :error-message="errorMessage"
      @submit="submitQuiz"
    />

    <QuizQuestionFormModal
      v-model:open="isQuestionFormOpen"
      :question="selectedQuestion"
      :is-submitting="isSavingQuestion"
      :error-message="errorMessage"
      @submit="submitQuestion"
    />

    <DeleteConfirmationModal
      v-model:open="isQuizDeleteOpen"
      title="Hapus quiz"
      :description="`Quiz '${quiz?.quiz_name ?? ''}' beserta seluruh pertanyaannya akan dihapus.`"
      :is-deleting="isDeletingQuiz"
      :error-message="errorMessage"
      @confirm="confirmDeleteQuiz"
    />

    <DeleteConfirmationModal
      v-model:open="isQuestionDeleteOpen"
      title="Hapus pertanyaan"
      :description="`Pertanyaan '${selectedQuestion?.question ?? ''}' akan dihapus dari quiz.`"
      :is-deleting="deletingQuestionId !== null"
      :error-message="errorMessage"
      @confirm="confirmDeleteQuestion"
    />
  </AdminDashboard>
</template>
