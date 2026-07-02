import { httpClient } from "@/shared/api/http-client";

import type {
  GetLearningCourseDetailResponse,
  GetStudentLessonResponse,
  SubmitLessonProgressRequest,
  SubmitLessonProgressResponse,
} from "../types/lesson.types";

export function getStudentLesson(lessonId: string): Promise<GetStudentLessonResponse> {
  return httpClient.get<GetStudentLessonResponse>(
    `lessons/${encodeURIComponent(lessonId)}`,
  );
}

export function submitLessonProgress(
  lessonId: string,
  payload: SubmitLessonProgressRequest,
): Promise<SubmitLessonProgressResponse> {
  return httpClient.post<SubmitLessonProgressResponse, SubmitLessonProgressRequest>(
    `lessons/${encodeURIComponent(lessonId)}/progress`,
    payload,
  );
}

export function getLearningCourseDetail(
  courseId: string,
): Promise<GetLearningCourseDetailResponse> {
  return httpClient.get<GetLearningCourseDetailResponse>(
    `courses/${encodeURIComponent(courseId)}`,
  );
}
