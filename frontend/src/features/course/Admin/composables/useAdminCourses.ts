import { computed, onBeforeUnmount, readonly, ref, watch } from 'vue'

import { ApiError } from '@/shared/api/api-error'
import {
  createCourse,
  deleteCourse,
  getAdminCourses,
  updateCourse,
} from '../api/course.api'
import type {
  CourseDto,
  CourseFormValues,
  CreateCourseRequest,
  UpdateCourseRequest,
} from '../types/course.types'

const PAGE_SIZE = 10
const SEARCH_DEBOUNCE_MS = 350

export function useAdminCourses() {
  const courses = ref<CourseDto[]>([])
  const page = ref(1)
  const total = ref(0)
  const lastPage = ref(1)
  const search = ref('')
  const isLoading = ref(false)
  const isSaving = ref(false)
  const deletingCourseId = ref<string | null>(null)
  const errorMessage = ref('')
  const successMessage = ref('')
  let requestSequence = 0
  let searchTimer: ReturnType<typeof setTimeout> | undefined

  const isDeleting = computed(() => deletingCourseId.value !== null)

  function getErrorMessage(error: unknown, fallback: string): string {
    return error instanceof ApiError ? error.message : fallback
  }

  async function fetchCourses(): Promise<void> {
    const requestId = ++requestSequence
    isLoading.value = true
    errorMessage.value = ''

    try {
      const response = await getAdminCourses({
        page: page.value,
        limit: PAGE_SIZE,
        search: search.value.trim() || undefined,
        sort_by: 'created_at',
        sort_direction: 'desc',
      })

      if (requestId !== requestSequence) return

      courses.value = response.data
      total.value = response.meta.total
      lastPage.value = response.meta.last_page

      if (page.value > response.meta.last_page && response.meta.last_page > 0) {
        page.value = response.meta.last_page
      }
    } catch (error) {
      if (requestId === requestSequence) {
        errorMessage.value = getErrorMessage(
          error,
          'Daftar course gagal dimuat. Silakan coba kembali.',
        )
      }
    } finally {
      if (requestId === requestSequence) isLoading.value = false
    }
  }

  async function saveCourse(
    values: CourseFormValues,
    courseId?: string,
  ): Promise<boolean> {
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
      const response = courseId
        ? await updateCourse(courseId, payload satisfies UpdateCourseRequest)
        : await createCourse(payload)

      successMessage.value = response.message
      await fetchCourses()
      return true
    } catch (error) {
      errorMessage.value = getErrorMessage(error, 'Course gagal disimpan.')
      return false
    } finally {
      isSaving.value = false
    }
  }

  async function removeCourse(courseId: string): Promise<boolean> {
    deletingCourseId.value = courseId
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const response = await deleteCourse(courseId)
      successMessage.value = response.message

      if (courses.value.length === 1 && page.value > 1) {
        page.value -= 1
      } else {
        await fetchCourses()
      }

      return true
    } catch (error) {
      errorMessage.value = getErrorMessage(error, 'Course gagal dihapus.')
      return false
    } finally {
      deletingCourseId.value = null
    }
  }

  function clearMessages(): void {
    errorMessage.value = ''
    successMessage.value = ''
  }

  watch(page, () => {
    void fetchCourses()
  })

  watch(search, () => {
    if (searchTimer) clearTimeout(searchTimer)
    searchTimer = setTimeout(() => {
      if (page.value === 1) void fetchCourses()
      else page.value = 1
    }, SEARCH_DEBOUNCE_MS)
  })

  onBeforeUnmount(() => {
    if (searchTimer) clearTimeout(searchTimer)
  })

  return {
    courses: readonly(courses),
    page,
    pageSize: PAGE_SIZE,
    total: readonly(total),
    lastPage: readonly(lastPage),
    search,
    isLoading: readonly(isLoading),
    isSaving: readonly(isSaving),
    isDeleting,
    errorMessage: readonly(errorMessage),
    successMessage: readonly(successMessage),
    fetchCourses,
    saveCourse,
    removeCourse,
    clearMessages,
  }
}
