import { readonly, ref } from 'vue'

import { ApiError } from '@/shared/api/api-error'
import { getAdminCourseDetail, updateCourse } from '../api/course.api'
import type {
  AdminCourseDetailDto,
  CourseFormValues,
  CreateCourseRequest,
} from '../types/course.types'

export function useAdminCourseDetail() {
  const course = ref<AdminCourseDetailDto | null>(null)
  const isLoading = ref(false)
  const isSaving = ref(false)
  const errorMessage = ref('')
  const successMessage = ref('')
  let requestSequence = 0

  function getErrorMessage(error: unknown, fallback: string): string {
    return error instanceof ApiError ? error.message : fallback
  }

  async function fetchCourse(courseId: string): Promise<void> {
    const requestId = ++requestSequence
    isLoading.value = true
    errorMessage.value = ''

    try {
      const response = await getAdminCourseDetail(courseId)

      if (requestId === requestSequence) course.value = response.data
    } catch (error) {
      if (requestId === requestSequence) {
        course.value = null
        errorMessage.value = getErrorMessage(
          error,
          'Detail course gagal dimuat. Silakan coba kembali.',
        )
      }
    } finally {
      if (requestId === requestSequence) isLoading.value = false
    }
  }

  async function saveCourse(
    courseId: string,
    values: CourseFormValues,
  ): Promise<boolean> {
    if (isSaving.value) return false

    isSaving.value = true
    errorMessage.value = ''
    successMessage.value = ''

    const payload: CreateCourseRequest = {
      course_name: values.courseName.trim(),
      description: values.description.trim(),
      minimum_score: values.minimumScore,
      status: values.status,
    }

    try {
      const response = await updateCourse(courseId, payload)
      successMessage.value = response.message
      await fetchCourse(courseId)
      return true
    } catch (error) {
      errorMessage.value = getErrorMessage(error, 'Course gagal diperbarui.')
      return false
    } finally {
      isSaving.value = false
    }
  }

  function clearMessages(): void {
    errorMessage.value = ''
    successMessage.value = ''
  }

  return {
    course: readonly(course),
    isLoading: readonly(isLoading),
    isSaving: readonly(isSaving),
    errorMessage: readonly(errorMessage),
    successMessage: readonly(successMessage),
    fetchCourse,
    saveCourse,
    clearMessages,
  }
}
