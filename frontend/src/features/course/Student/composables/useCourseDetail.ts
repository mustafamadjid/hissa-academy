import { readonly, ref } from "vue";

import {
  getCourseCatalog,
  getStudentCourseDetail,
} from "../api/course-catalog.api";
import type { StudentCourseDetailDto } from "../types/course-catalog.types";

export function useCourseDetail() {
  const course = ref<StudentCourseDetailDto | null>(null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  async function fetchCourse(
    courseId: string,
    isAuthenticated: boolean,
  ): Promise<void> {
    isLoading.value = true;
    error.value = null;

    try {
      if (isAuthenticated) {
        const response = await getStudentCourseDetail(courseId);
        course.value = response.data;
        return;
      }

      const response = await getCourseCatalog({ limit: 100 });
      const summary = response.data.find((item) => item.id === courseId);
      course.value = summary ? { ...summary, lessons: [] } : null;
      if (!summary) error.value = "Course tidak ditemukan.";
    } catch (caughtError: unknown) {
      course.value = null;
      error.value =
        caughtError instanceof Error
          ? caughtError.message
          : "Detail course gagal dimuat.";
    } finally {
      isLoading.value = false;
    }
  }

  return {
    course: readonly(course),
    isLoading: readonly(isLoading),
    error: readonly(error),
    fetchCourse,
  };
}
