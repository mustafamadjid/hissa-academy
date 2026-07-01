import { computed, readonly, ref } from "vue";

import { generateYoutubeEmbedUrl } from "@/features/course/utils/youtube-video";
import { ApiError } from "@/shared/api/api-error";

import { getStudentLesson } from "../api/lesson.api";
import type { StudentLessonDetailDto } from "../types/lesson.types";

export function useLessonDetail() {
  const lesson = ref<StudentLessonDetailDto | null>(null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);
  const isLocked = ref(false);

  const embedUrl = computed(() => {
    const videoId = lesson.value?.video?.youtube_video_id;
    return videoId ? generateYoutubeEmbedUrl(videoId) : null;
  });

  async function fetchLesson(lessonId: string): Promise<void> {
    isLoading.value = true;
    error.value = null;
    isLocked.value = false;
    try {
      const response = await getStudentLesson(lessonId);
      lesson.value = response.data;
    } catch (caughtError: unknown) {
      lesson.value = null;
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
    isLoading: readonly(isLoading),
    error: readonly(error),
    isLocked: readonly(isLocked),
    embedUrl,
    fetchLesson,
  };
}
