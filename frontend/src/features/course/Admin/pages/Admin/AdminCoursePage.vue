<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import {
  AlertCircle,
  BookOpen,
  CheckCircle2,
  FileText,
  Layers,
  Plus,
  Search,
} from "@lucide/vue";

import AdminDashboard from "@/layouts/AdminDashboard/AdminDashboard.vue";
import DeleteConfirmationModal from "@/shared/components/ui/DeleteConfirmationModal.vue";
import CourseFormModal from "../../components/CourseFormModal.vue";
import CourseTable from "../../components/CourseTable.vue";
import { useAdminCourses } from "../../composables/useAdminCourses";
import type {
  CourseDto,
  CourseFormValues,
} from "@/features/course/Admin/types/course.types.ts";

const {
  courses,
  page,
  pageSize,
  total,
  search,
  isLoading,
  isSaving,
  isDeleting,
  errorMessage,
  successMessage,
  fetchCourses,
  saveCourse,
  removeCourse,
  clearMessages,
} = useAdminCourses();

const router = useRouter();

const isFormOpen = ref(false);
const selectedCourse = ref<CourseDto | null>(null);
const courseToDelete = ref<CourseDto | null>(null);

function openCreateForm(): void {
  clearMessages();
  selectedCourse.value = null;
  isFormOpen.value = true;
}

function openEditForm(course: CourseDto): void {
  clearMessages();
  selectedCourse.value = course;
  isFormOpen.value = true;
}

function requestDelete(course: CourseDto): void {
  clearMessages();
  courseToDelete.value = course;
}

function openCourseDetail(course: CourseDto): void {
  void router.push({
    name: "admin-course-detail",
    params: { courseId: course.id },
  });
}

async function submitCourse(values: CourseFormValues): Promise<void> {
  const saved = await saveCourse(values, selectedCourse.value?.id);

  if (saved) {
    isFormOpen.value = false;
  }
}

async function confirmDelete(): Promise<void> {
  if (!courseToDelete.value) return;

  const deleted = await removeCourse(courseToDelete.value.id);

  if (deleted) {
    courseToDelete.value = null;
  }
}

onMounted(() => {
  void fetchCourses();
});
</script>

<template>
  <AdminDashboard>
    <main class="mx-auto w-full max-w-350 px-4 py-6 md:px-6 md:py-8">
      <!-- Page Header -->
      <header
        class="flex flex-col gap-5 border-b border-slate-200 pb-6 md:flex-row md:items-end md:justify-between"
      >
        <div class="max-w-2xl">
          <div
            class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-slate-500"
          >
            <Layers :size="14" stroke-width="1.8" />
            <span>Learning Management</span>
          </div>

          <h1
            class="text-2xl font-bold tracking-tight text-slate-950 md:text-3xl"
          >
            Kelola Course
          </h1>

          <p class="mt-2 text-sm leading-6 text-slate-500 md:text-base">
            Kelola informasi course, susunan materi, dan konten pembelajaran
            HISSA Academy.
          </p>
        </div>

        <UButton
          size="lg"
          color="neutral"
          class="cursor-pointer justify-center rounded-lg bg-primary px-4 text-white shadow-none transition-colors hover:bg-primary/90 active:bg-slate-950"
          @click="openCreateForm"
        >
          <Plus :size="18" />
          Tambah Course
        </UButton>
      </header>

      <!-- Summary -->
      <section
        class="grid grid-cols-1 border-b border-slate-200 md:grid-cols-[240px_1fr]"
        aria-label="Ringkasan course"
      >
        <div
          class="flex items-center gap-4 py-5 md:border-r md:border-slate-200 md:pr-6"
        >
          <div
            class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600"
          >
            <BookOpen :size="19" stroke-width="1.8" />
          </div>

          <div>
            <p
              class="text-xs font-medium uppercase tracking-wide text-slate-500"
            >
              Total Course
            </p>

            <p class="mt-0.5 text-2xl font-semibold text-slate-950">
              {{ total }}
            </p>
          </div>
        </div>

        <div
          class="flex items-start gap-4 border-t border-slate-200 py-5 md:border-t-0 md:pl-6"
        >
          <div
            class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600"
          >
            <FileText :size="19" stroke-width="1.8" />
          </div>

          <div>
            <p class="text-sm font-semibold text-slate-800">
              Pengelolaan konten
            </p>

            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
              Modul dan lesson dapat dikelola melalui halaman detail setelah
              informasi utama course disimpan.
            </p>
          </div>
        </div>
      </section>

      <!-- Feedback Messages -->
      <TransitionGroup name="feedback" tag="div" class="mt-6 space-y-3">
        <div
          v-if="successMessage"
          key="success"
          role="status"
          class="flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50/50 px-4 py-3 text-emerald-800"
        >
          <CheckCircle2 :size="18" class="mt-0.5 shrink-0" stroke-width="1.8" />

          <span class="text-sm leading-5">
            {{ successMessage }}
          </span>
        </div>

        <div
          v-if="errorMessage"
          key="error"
          role="alert"
          class="flex flex-col gap-3 rounded-lg border border-red-200 bg-red-50/50 px-4 py-3 text-red-800 sm:flex-row sm:items-center sm:justify-between"
        >
          <div class="flex items-start gap-3">
            <AlertCircle
              :size="18"
              class="mt-0.5 shrink-0"
              stroke-width="1.8"
            />

            <span class="text-sm leading-5">
              {{ errorMessage }}
            </span>
          </div>

          <UButton
            v-if="!isFormOpen && !courseToDelete"
            label="Muat ulang"
            color="neutral"
            variant="ghost"
            size="sm"
            icon="i-heroicons-arrow-path"
            class="cursor-pointer self-start text-slate-700 hover:bg-slate-100 hover:text-slate-900 sm:self-auto"
            @click="fetchCourses"
          />
        </div>
      </TransitionGroup>

      <!-- Course List -->
      <section
        class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white"
      >
        <div
          class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between md:px-5"
        >
          <div>
            <h2 class="text-sm font-semibold text-slate-900">Daftar Course</h2>

            <p class="mt-1 text-xs text-slate-500">
              {{ total }} course tersedia
            </p>
          </div>

          <div class="w-full sm:max-w-sm">
            <UInput
              v-model="search"
              type="search"
              placeholder="Cari course..."
              size="md"
              variant="outline"
              class="w-full"
              :ui="{
                base: 'rounded-lg border-slate-200 bg-white text-slate-900 shadow-none placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-200',
              }"
            >
              <template #leading>
                <Search :size="17" class="text-slate-400" stroke-width="1.8" />
              </template>
            </UInput>
          </div>
        </div>

        <div class="p-2 md:p-3">
          <CourseTable
            v-model:page="page"
            :courses="courses"
            :page-size="pageSize"
            :total="total"
            :is-loading="isLoading"
            @view="openCourseDetail"
            @edit="openEditForm"
            @delete="requestDelete"
          />
        </div>
      </section>
    </main>

    <CourseFormModal
      v-model:open="isFormOpen"
      :course="selectedCourse"
      :is-submitting="isSaving"
      :error-message="errorMessage"
      @submit="submitCourse"
    />

    <DeleteConfirmationModal
      :open="courseToDelete !== null"
      title="Konfirmasi Hapus"
      :description="`Apakah Anda yakin ingin menghapus course “${courseToDelete?.name}”? Data yang sudah dihapus tidak dapat dikembalikan.`"
      :is-deleting="isDeleting"
      :error-message="errorMessage"
      @update:open="courseToDelete = null"
      @confirm="confirmDelete"
    />
  </AdminDashboard>
</template>

<style scoped>
.feedback-enter-active,
.feedback-leave-active {
  transition:
    opacity 180ms ease,
    transform 180ms ease;
}

.feedback-enter-from,
.feedback-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
