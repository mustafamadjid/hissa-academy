import { computed, readonly, ref } from "vue";

import { ApiError } from "@/shared/api/api-error";

import { getLearningCourseDetail, getStudentLesson } from "../api/lesson.api";
import type {
  LearningCourseDetailDto,
  StudentLessonDetailDto,
} from "../types/lesson.types";

export function useLessonDetail() {
  const lesson = ref<StudentLessonDetailDto | null>(null);
  const course = ref<LearningCourseDetailDto | null>(null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);
  const isLocked = ref(false);

  const embedUrl = computed(() => {
    const videoId = lesson.value?.video?.youtube_video_id;
    return videoId
      ? `https://www.youtube-nocookie.com/embed/${encodeURIComponent(videoId)}`
      : null;
  });

  async function fetchLesson(lessonId: string): Promise<void> {
    isLoading.value = true;
    error.value = null;
    isLocked.value = false;
    try {
      const response = await getStudentLesson(lessonId);
      lesson.value = response.data;
      const courseResponse = await getLearningCourseDetail(response.data.course_id);
      course.value = courseResponse.data;
    } catch (caughtError: unknown) {
      lesson.value = null;
      course.value = null;
      isLocked.value =
        caughtError instanceof ApiError && caughtError.statusCode === 403;
      error.value =
        caughtError instanceof Error
          ? caughtError.message
          : "Lesson gagal dimuat.";
    } finally {
      isLoading.value = false;
    }
  }

  return {
    lesson: readonly(lesson),
    course: readonly(course),
    isLoading: readonly(isLoading),
    error: readonly(error),
    isLocked: readonly(isLocked),
    embedUrl,
    fetchLesson,
  };
}
