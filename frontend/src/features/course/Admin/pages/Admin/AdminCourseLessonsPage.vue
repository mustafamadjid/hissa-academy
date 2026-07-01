<script setup lang="ts">
import { computed, ref, watch } from "vue";
import {
  AlertCircle,
  ArrowLeft,
  CheckCircle2,
  ListVideo,
  Plus,
  RotateCcw,
} from "@lucide/vue";
import { useRoute } from "vue-router";

import AdminDashboard from "@/layouts/AdminDashboard/AdminDashboard.vue";
import DeleteConfirmationModal from "@/shared/components/ui/DeleteConfirmationModal.vue";
import LessonCreateModal from "../../components/LessonCreateModal.vue";
import LessonDraggableList from "../../components/LessonDraggableList.vue";
import LessonEditModal from "../../components/LessonEditModal.vue";
import { useAdminCourseLessons } from "../../composables/useAdminCourseLessons";
import type {
  AdminLessonDto,
  CreateLessonFormValues,
  LessonFormValues,
} from "../../../types/course.types.ts";

const route = useRoute();
const courseId = computed(() =>
  typeof route.params.courseId === "string" ? route.params.courseId : "",
);
const selectedLesson = ref<AdminLessonDto | null>(null);
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const {
  course,
  lessons,
  isLoading,
  isCreating,
  isReordering,
  savingLessonId,
  deletingLessonId,
  errorMessage,
  successMessage,
  fetchPage,
  createLesson,
  reorderLessons,
  saveLesson,
  removeLesson,
  clearMessages,
} = useAdminCourseLessons();

const isListDisabled = computed(
  () =>
    isReordering.value ||
    isCreating.value ||
    savingLessonId.value !== null ||
    deletingLessonId.value !== null,
);

function openCreateModal(): void {
  clearMessages();
  isCreateModalOpen.value = true;
}

async function submitNewLesson(values: CreateLessonFormValues): Promise<void> {
  if (!courseId.value) return;

  const created = await createLesson(courseId.value, values);

  if (created) isCreateModalOpen.value = false;
}

function openEditModal(lesson: AdminLessonDto): void {
  clearMessages();
  selectedLesson.value = lesson;
  isEditModalOpen.value = true;
}

function openDeleteModal(lesson: AdminLessonDto): void {
  clearMessages();
  selectedLesson.value = lesson;
  isDeleteModalOpen.value = true;
}

async function submitLesson(values: LessonFormValues): Promise<void> {
  if (!courseId.value || !selectedLesson.value) return;

  const saved = await saveLesson(
    courseId.value,
    selectedLesson.value.id,
    values,
  );

  if (saved) {
    isEditModalOpen.value = false;
    selectedLesson.value = null;
  }
}

async function confirmDelete(): Promise<void> {
  if (!courseId.value || !selectedLesson.value) return;

  const deleted = await removeLesson(courseId.value, selectedLesson.value.id);

  if (deleted) {
    isDeleteModalOpen.value = false;
    selectedLesson.value = null;
  }
}

function handleReorder(orderedLessons: AdminLessonDto[]): void {
  if (!courseId.value) return;

  void reorderLessons(courseId.value, orderedLessons);
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
        <span class="font-semibold text-slate-700">Kelola Lesson</span>
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
        v-if="errorMessage && course"
        role="alert"
        class="mb-5 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800"
      >
        <AlertCircle :size="18" class="mt-0.5 shrink-0" aria-hidden="true" />
        <span class="text-sm">{{ errorMessage }}</span>
      </div>

      <div
        v-if="isLoading && !course"
        class="space-y-6"
        role="status"
        aria-label="Memuat daftar lesson"
      >
        <USkeleton class="h-32 w-full rounded-xl" />
        <div class="space-y-3">
          <USkeleton
            v-for="index in 3"
            :key="index"
            class="h-24 w-full rounded-xl"
          />
        </div>
      </div>

      <div
        v-else-if="!course"
        role="alert"
        class="flex min-h-80 flex-col items-center justify-center rounded-xl border border-red-200 bg-white px-6 py-12 text-center"
      >
        <span
          class="mb-4 flex size-12 items-center justify-center rounded-full bg-red-50 text-red-600"
        >
          <AlertCircle :size="23" aria-hidden="true" />
        </span>
        <h1 class="text-lg font-bold text-slate-900">
          Lesson course tidak tersedia
        </h1>
        <p class="mt-2 max-w-md text-sm leading-6 text-slate-500">
          {{ errorMessage || "Course tidak valid atau sudah tidak tersedia." }}
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
                <ListVideo :size="24" aria-hidden="true" />
              </span>
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wider text-emerald-300"
                >
                  Kelola Lesson
                </p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                  {{ course.name }}
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-emerald-100/75">
                  Atur urutan materi, perbarui judul, dan kelola video YouTube
                  setiap lesson.
                </p>
              </div>
            </div>

            <UButton
              color="primary"
              variant="solid"
              class="w-full justify-center bg-white text-emerald-900 hover:bg-emerald-50 sm:w-auto"
              :disabled="isListDisabled"
              @click="openCreateModal"
            >
              <Plus :size="17" aria-hidden="true" />
              Tambah Lesson
            </UButton>
          </div>
        </header>

        <LessonDraggableList
          :lessons="lessons"
          :disabled="isListDisabled"
          @edit="openEditModal"
          @delete="openDeleteModal"
          @reorder="handleReorder"
        />
      </div>
    </main>

    <LessonCreateModal
      v-model:open="isCreateModalOpen"
      :is-submitting="isCreating"
      :error-message="errorMessage"
      @submit="submitNewLesson"
    />

    <LessonEditModal
      v-model:open="isEditModalOpen"
      :lesson="selectedLesson"
      :is-submitting="savingLessonId !== null"
      :error-message="errorMessage"
      @submit="submitLesson"
    />

    <DeleteConfirmationModal
      v-model:open="isDeleteModalOpen"
      title="Hapus lesson"
      :description="`Lesson '${selectedLesson?.title ?? ''}' akan dihapus dari course.`"
      :is-deleting="deletingLessonId !== null"
      :error-message="errorMessage"
      @confirm="confirmDelete"
    />
  </AdminDashboard>
</template>
