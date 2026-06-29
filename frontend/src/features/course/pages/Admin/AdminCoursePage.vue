<script setup lang="ts">
import { onMounted, ref } from "vue";
import {
  BookOpen,
  Plus,
  Search,
  CheckCircle2,
  AlertCircle,
  FileText,
  Layers,
} from "@lucide/vue";

import AdminDashboard from "@/layouts/AdminDashboard/AdminDashboard.vue";
import DeleteConfirmationModal from "@/shared/components/ui/DeleteConfirmationModal.vue";
import CourseFormModal from "../../components/CourseFormModal.vue";
import CourseTable from "../../components/CourseTable.vue";
import { useAdminCourses } from "../../composables/useAdminCourses";
import type { CourseDto, CourseFormValues } from "../../types/course.types";

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

async function submitCourse(values: CourseFormValues): Promise<void> {
  const saved = await saveCourse(values, selectedCourse.value?.id);
  if (saved) isFormOpen.value = false;
}

async function confirmDelete(): Promise<void> {
  if (!courseToDelete.value) return;
  const deleted = await removeCourse(courseToDelete.value.id);
  if (deleted) courseToDelete.value = null;
}

onMounted(() => {
  void fetchCourses();
});
</script>

<template>
  <AdminDashboard>
    <div class="max-w-[1400px] mx-auto space-y-8 p-4 md:p-6">
      <!-- HEADER SECTION -->
      <header
        class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
      >
        <div class="space-y-1">
          <div
            class="flex items-center gap-2 text-emerald-600 font-semibold text-sm tracking-wide uppercase"
          >
            <Layers :size="16" />
            <span>Learning Management</span>
          </div>
          <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
            Kelola Course
          </h1>
          <p class="text-slate-500 max-w-2xl">
            Pusat kendali konten pembelajaran. Tambah, edit, atau pantau
            performa course HISSA Academy di sini.
          </p>
        </div>

        <UButton
          class="cursor-pointer p-4 shadow-sm hover:shadow-md transition-all duration-200"
          size="xl"
          color="primary"
          @click="openCreateForm"
        >
          <Plus :size="20" class="mr-1" />
          Tambah Course Baru
        </UButton>
      </header>

      <!-- STATS GRID -->
      <section
        class="grid grid-cols-1 md:grid-cols-3 gap-4"
        aria-label="Statistik Ringkas"
      >
        <!-- Total Course -->
        <div
          class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 transition-all hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5"
        >
          <div class="flex items-center gap-4">
            <div
              class="rounded-xl bg-emerald-50 p-3 text-emerald-600 group-hover:bg-emerald-100 transition-colors"
            >
              <BookOpen :size="24" />
            </div>
            <div>
              <p
                class="text-sm font-medium text-slate-500 uppercase tracking-wider"
              >
                Total Course
              </p>
              <h3 class="text-3xl font-bold text-slate-900">{{ total }}</h3>
            </div>
          </div>
        </div>

        <!-- Info Card (Status Konten) -->
        <div
          class="md:col-span-2 flex flex-col justify-center rounded-2xl border border-slate-200 bg-slate-50/50 p-6"
        >
          <div class="flex gap-4 items-start">
            <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
              <FileText :size="24" />
            </div>
            <div>
              <p class="text-sm font-bold text-slate-800">Tips Pengelolaan</p>
              <p class="mt-1 text-sm leading-relaxed text-slate-600">
                Gunakan fitur "Cari" untuk menemukan course secara spesifik.
                Pengaturan modul dan lesson tersedia di dalam detail
                masing-masing course setelah data utama disimpan.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- FEEDBACK MESSAGES -->
      <TransitionGroup name="fade">
        <div
          v-if="successMessage"
          key="success"
          class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm"
        >
          <CheckCircle2 :size="20" />
          <span class="text-sm font-semibold">{{ successMessage }}</span>
        </div>

        <div
          v-if="errorMessage"
          key="error"
          class="flex flex-col gap-4 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm md:flex-row md:items-center md:justify-between"
        >
          <div class="flex items-center gap-3">
            <AlertCircle :size="20" />
            <span class="text-sm font-semibold">{{ errorMessage }}</span>
          </div>
          <UButton
            v-if="!isFormOpen && !courseToDelete"
            label="Coba Muat Ulang"
            color="primary"
            variant="ghost"
            size="sm"
            icon="i-heroicons-arrow-path"
            @click="fetchCourses"
          />
        </div>
      </TransitionGroup>

      <!-- TABLE CONTROLS & DATA TABLE -->
      <div
        class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden"
      >
        <!-- Search Bar Area -->
        <div class="border-b border-slate-100 bg-slate-50/30 p-5">
          <div class="max-w-md">
            <UInput
              v-model="search"
              type="search"
              placeholder="Cari berdasarkan nama atau kategori..."
              class="w-full shadow-sm"
              size="md"
              icon="i-heroicons-magnifying-glass"
            >
              <template #leading>
                <Search :size="18" class="text-slate-400" />
              </template>
            </UInput>
          </div>
        </div>

        <!-- The Table -->
        <div class="p-2">
          <CourseTable
            v-model:page="page"
            :courses="courses"
            :page-size="pageSize"
            :total="total"
            :is-loading="isLoading"
            @edit="openEditForm"
            @delete="requestDelete"
          />
        </div>
      </div>
    </div>

    <!-- MODALS -->
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
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
