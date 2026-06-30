<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { AlertCircle, ArrowLeft, CheckCircle2, RotateCcw } from '@lucide/vue'
import { useRoute, useRouter } from 'vue-router'

import AdminDashboard from '@/layouts/AdminDashboard/AdminDashboard.vue'
import CourseDetailHero from '../../components/CourseDetailHero.vue'
import CourseFormModal from '../../components/CourseFormModal.vue'
import CourseLessonList from '../../components/CourseLessonList.vue'
import { useAdminCourseDetail } from '../../composables/useAdminCourseDetail'
import type { CourseFormValues } from '../../types/course.types'

const route = useRoute()
const router = useRouter()
const courseId = computed(() =>
  typeof route.params.courseId === 'string' ? route.params.courseId : '',
)
const isEditFormOpen = ref(false)

const {
  course,
  isLoading,
  isSaving,
  errorMessage,
  successMessage,
  fetchCourse,
  saveCourse,
  clearMessages,
} = useAdminCourseDetail()

function openEditForm(): void {
  clearMessages()
  isEditFormOpen.value = true
}

async function submitCourse(values: CourseFormValues): Promise<void> {
  if (!courseId.value) return

  const saved = await saveCourse(courseId.value, values)

  if (saved) isEditFormOpen.value = false
}

function retryFetch(): void {
  if (courseId.value) void fetchCourse(courseId.value)
}

function openLessonManagement(): void {
  if (!courseId.value) return

  void router.push(`/admin/courses/${courseId.value}/lessons`)
}

function openQuizManagement(): void {
  if (!courseId.value) return

  void router.push({
    name: 'admin-course-quiz',
    params: { courseId: courseId.value },
  })
}

watch(
  courseId,
  (id) => {
    if (id) void fetchCourse(id)
  },
  { immediate: true },
)
</script>

<template>
  <AdminDashboard>
    <main class="mx-auto w-full max-w-350 px-4 py-6 md:px-6 md:py-8">
      <nav aria-label="Breadcrumb" class="mb-5 flex items-center gap-3 text-xs">
        <RouterLink
          :to="{ name: 'admin-courses' }"
          class="inline-flex items-center gap-1.5 font-medium text-slate-500 transition-colors hover:text-emerald-700 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-emerald-600"
        >
          <ArrowLeft :size="14" aria-hidden="true" />
          Kembali ke Daftar
        </RouterLink>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <span class="font-semibold text-slate-700">Detail Course</span>
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
        v-if="isLoading && !course"
        class="space-y-7"
        role="status"
        aria-label="Memuat detail course"
      >
        <USkeleton class="h-64 w-full rounded-xl" />
        <div class="space-y-3">
          <USkeleton class="h-7 w-56" />
          <USkeleton class="h-48 w-full rounded-xl" />
        </div>
      </div>

      <div
        v-else-if="errorMessage && !course"
        role="alert"
        class="flex min-h-80 flex-col items-center justify-center rounded-xl border border-red-200 bg-white px-6 py-12 text-center"
      >
        <span
          class="mb-4 flex size-12 items-center justify-center rounded-full bg-red-50 text-red-600"
        >
          <AlertCircle :size="23" aria-hidden="true" />
        </span>
        <h1 class="text-lg font-bold text-slate-900">Detail course tidak tersedia</h1>
        <p class="mt-2 max-w-md text-sm leading-6 text-slate-500">
          {{ errorMessage }}
        </p>
        <UButton
          color="neutral"
          variant="outline"
          class="mt-5 cursor-pointer"
          @click="retryFetch"
        >
          <RotateCcw :size="16" aria-hidden="true" />
          Muat ulang
        </UButton>
      </div>

      <div v-else-if="course" class="space-y-7">
        <CourseDetailHero
          :course="course"
          @edit="openEditForm"
          @manage-lessons="openLessonManagement"
          @manage-quiz="openQuizManagement"
        />
        <CourseLessonList :lessons="course.lessons" />
      </div>
    </main>

    <CourseFormModal
      v-if="course"
      v-model:open="isEditFormOpen"
      :course="course"
      :is-submitting="isSaving"
      :error-message="errorMessage"
      @submit="submitCourse"
    />
  </AdminDashboard>
</template>
