import { httpClient } from "@/shared/api/http-client";

import type {
  GetLearningCourseDetailResponse,
  GetStudentLessonResponse,
} from "../types/lesson.types";

export function getStudentLesson(lessonId: string): Promise<GetStudentLessonResponse> {
  return httpClient.get<GetStudentLessonResponse>(
    `lessons/${encodeURIComponent(lessonId)}`,
  );
}

export function getLearningCourseDetail(
  courseId: string,
): Promise<GetLearningCourseDetailResponse> {
  return httpClient.get<GetLearningCourseDetailResponse>(
    `courses/${encodeURIComponent(courseId)}`,
  );
}
