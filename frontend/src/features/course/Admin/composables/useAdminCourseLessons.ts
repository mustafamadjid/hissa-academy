import { readonly, ref } from 'vue'

import { ApiError } from '@/shared/api/api-error'
import {
  createAdminCourseLesson,
  deleteAdminLesson,
  deleteAdminLessonVideo,
  getAdminCourseDetail,
  getAdminCourseLessons,
  reorderAdminCourseLessons,
  updateAdminLesson,
  upsertAdminLessonVideo,
} from '../api/course.api'
import type {
  AdminLessonDto,
  CourseDto,
  CreateLessonFormValues,
  CreateLessonRequest,
  LessonFormValues,
  ReorderLessonsRequest,
} from '../types/course.types'
import { extractYoutubeVideoId } from '../utils/youtube-video'

export function useAdminCourseLessons() {
  const course = ref<CourseDto | null>(null)
  const lessons = ref<AdminLessonDto[]>([])
  const isLoading = ref(false)
  const isReordering = ref(false)
  const isCreating = ref(false)
  const savingLessonId = ref<string | null>(null)
  const deletingLessonId = ref<string | null>(null)
  const errorMessage = ref('')
  const successMessage = ref('')
  let requestSequence = 0

  function resolveError(error: unknown, fallback: string): string {
    return error instanceof ApiError ? error.message : fallback
  }

  async function fetchPage(courseId: string): Promise<void> {
    const requestId = ++requestSequence
    isLoading.value = true
    errorMessage.value = ''

    try {
      const [courseResponse, lessonsResponse] = await Promise.all([
        getAdminCourseDetail(courseId),
        getAdminCourseLessons(courseId),
      ])

      if (requestId !== requestSequence) return

      course.value = courseResponse.data
      lessons.value = [...lessonsResponse.data].sort(
        (first, second) => first.position - second.position,
      )
    } catch (error) {
      if (requestId === requestSequence) {
        course.value = null
        lessons.value = []
        errorMessage.value = resolveError(
          error,
          'Daftar lesson gagal dimuat. Silakan coba kembali.',
        )
      }
    } finally {
      if (requestId === requestSequence) isLoading.value = false
    }
  }

  async function refreshLessons(courseId: string): Promise<void> {
    const response = await getAdminCourseLessons(courseId)
    lessons.value = [...response.data].sort(
      (first, second) => first.position - second.position,
    )
  }

  async function reorderLessons(
    courseId: string,
    orderedLessons: readonly AdminLessonDto[],
  ): Promise<boolean> {
    if (isReordering.value || orderedLessons.length === 0) return false

    const previousLessons = [...lessons.value]
    const reorderedLessons = orderedLessons.map((lesson, index) => ({
      ...lesson,
      position: index + 1,
    }))
    const body: ReorderLessonsRequest = {
      lessons: reorderedLessons.map(({ id, position }) => ({ id, position })),
    }

    lessons.value = reorderedLessons
    isReordering.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const response = await reorderAdminCourseLessons(courseId, body)
      successMessage.value = response.message
      return true
    } catch (error) {
      lessons.value = previousLessons
      errorMessage.value = resolveError(
        error,
        'Urutan lesson gagal disimpan. Urutan sebelumnya telah dipulihkan.',
      )
      return false
    } finally {
      isReordering.value = false
    }
  }

  async function createLesson(
    courseId: string,
    values: CreateLessonFormValues,
  ): Promise<boolean> {
    if (isCreating.value) return false

    const youtubeVideoId = extractYoutubeVideoId(values.videoUrl)
    if (!youtubeVideoId) {
      errorMessage.value = 'Link video YouTube tidak valid.'
      return false
    }

    const body: CreateLessonRequest = {
      title: values.title.trim(),
      youtube_video_id: youtubeVideoId,
      position: lessons.value.length + 1,
      is_required: values.isRequired,
    }

    isCreating.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const response = await createAdminCourseLesson(courseId, body)
      await refreshLessons(courseId)
      successMessage.value = response.message
      return true
    } catch (error) {
      errorMessage.value = resolveError(error, 'Lesson gagal ditambahkan.')
      return false
    } finally {
      isCreating.value = false
    }
  }

  async function saveLesson(
    courseId: string,
    lessonId: string,
    values: LessonFormValues,
  ): Promise<boolean> {
    if (savingLessonId.value) return false

    const currentLesson = lessons.value.find((lesson) => lesson.id === lessonId)
    if (!currentLesson) return false

    const videoUrl = values.videoUrl.trim()
    const youtubeVideoId = videoUrl ? extractYoutubeVideoId(videoUrl) : null

    if (videoUrl && !youtubeVideoId) {
      errorMessage.value = 'Link video YouTube tidak valid.'
      return false
    }

    savingLessonId.value = lessonId
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const titleResponse = await updateAdminLesson(lessonId, {
        title: values.title.trim(),
      })

      if (!videoUrl && currentLesson.video) {
        await deleteAdminLessonVideo(lessonId)
      } else if (
        youtubeVideoId &&
        youtubeVideoId !== currentLesson.video?.youtube_video_id
      ) {
        await upsertAdminLessonVideo(lessonId, {
          youtube_video_id: youtubeVideoId,
        })
      }

      await refreshLessons(courseId)
      successMessage.value = titleResponse.message
      return true
    } catch (error) {
      try {
        await refreshLessons(courseId)
      } catch {
        // Pertahankan error operasi utama; refresh berikutnya tetap tersedia dari halaman.
      }

      errorMessage.value = resolveError(error, 'Lesson gagal diperbarui.')
      return false
    } finally {
      savingLessonId.value = null
    }
  }

  async function removeLesson(
    courseId: string,
    lessonId: string,
  ): Promise<boolean> {
    if (deletingLessonId.value) return false

    deletingLessonId.value = lessonId
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const response = await deleteAdminLesson(lessonId)
      await refreshLessons(courseId)
      successMessage.value = response.message
      return true
    } catch (error) {
      errorMessage.value = resolveError(error, 'Lesson gagal dihapus.')
      return false
    } finally {
      deletingLessonId.value = null
    }
  }

  function clearMessages(): void {
    errorMessage.value = ''
    successMessage.value = ''
  }

  return {
    course: readonly(course),
    lessons: readonly(lessons),
    isLoading: readonly(isLoading),
    isReordering: readonly(isReordering),
    isCreating: readonly(isCreating),
    savingLessonId: readonly(savingLessonId),
    deletingLessonId: readonly(deletingLessonId),
    errorMessage: readonly(errorMessage),
    successMessage: readonly(successMessage),
    fetchPage,
    reorderLessons,
    createLesson,
    saveLesson,
    removeLesson,
    clearMessages,
  }
}
