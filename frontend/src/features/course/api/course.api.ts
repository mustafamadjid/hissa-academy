import { httpClient } from "@/shared/api/http-client";

import type {
  CreateCourseRequest,
  CreateCourseResponse,
  CreateLessonRequest,
  CreateLessonResponse,
  DeleteCourseResponse,
  GetAdminCourseLessonsResponse,
  GetAdminCourseDetailResponse,
  GetAdminCoursesQuery,
  GetAdminCoursesResponse,
  UpdateCourseRequest,
  UpdateCourseResponse,
  ReorderLessonsRequest,
  SimpleLessonActionResponse,
  UpdateLessonRequest,
  UpdateLessonResponse,
  UpsertLessonVideoRequest,
  UpsertLessonVideoResponse,
} from "../types/course.types";

const ADMIN_COURSES_ENDPOINT = "admin/courses";

export function getAdminCourses(
  query: GetAdminCoursesQuery,
): Promise<GetAdminCoursesResponse> {
  return httpClient.get<GetAdminCoursesResponse>(ADMIN_COURSES_ENDPOINT, {
    params: query,
  });
}

export function getAdminCourseDetail(
  courseId: string,
): Promise<GetAdminCourseDetailResponse> {
  return httpClient.get<GetAdminCourseDetailResponse>(
    `${ADMIN_COURSES_ENDPOINT}/${courseId}`,
  );
}

export function createCourse(
  body: CreateCourseRequest,
): Promise<CreateCourseResponse> {
  return httpClient.post<CreateCourseResponse, CreateCourseRequest>(
    ADMIN_COURSES_ENDPOINT,
    body,
  );
}

export function updateCourse(
  courseId: string,
  body: UpdateCourseRequest,
): Promise<UpdateCourseResponse> {
  return httpClient.patch<UpdateCourseResponse, UpdateCourseRequest>(
    `${ADMIN_COURSES_ENDPOINT}/${courseId}`,
    body,
  );
}

export function deleteCourse(courseId: string): Promise<DeleteCourseResponse> {
  return httpClient.delete<DeleteCourseResponse>(
    `${ADMIN_COURSES_ENDPOINT}/${courseId}`,
  );
}

export function getAdminCourseLessons(
  courseId: string,
): Promise<GetAdminCourseLessonsResponse> {
  return httpClient.get<GetAdminCourseLessonsResponse>(
    `${ADMIN_COURSES_ENDPOINT}/${courseId}/lessons`,
  );
}

export function createAdminCourseLesson(
  courseId: string,
  body: CreateLessonRequest,
): Promise<CreateLessonResponse> {
  return httpClient.post<CreateLessonResponse, CreateLessonRequest>(
    `${ADMIN_COURSES_ENDPOINT}/${courseId}/lessons`,
    body,
  );
}

export function reorderAdminCourseLessons(
  courseId: string,
  body: ReorderLessonsRequest,
): Promise<SimpleLessonActionResponse> {
  return httpClient.patch<SimpleLessonActionResponse, ReorderLessonsRequest>(
    `${ADMIN_COURSES_ENDPOINT}/${courseId}/lessons/reorder`,
    body,
  );
}

export function updateAdminLesson(
  lessonId: string,
  body: UpdateLessonRequest,
): Promise<UpdateLessonResponse> {
  return httpClient.patch<UpdateLessonResponse, UpdateLessonRequest>(
    `admin/lessons/${lessonId}`,
    body,
  );
}

export function deleteAdminLesson(
  lessonId: string,
): Promise<SimpleLessonActionResponse> {
  return httpClient.delete<SimpleLessonActionResponse>(
    `admin/lessons/${lessonId}`,
  );
}

export function upsertAdminLessonVideo(
  lessonId: string,
  body: UpsertLessonVideoRequest,
): Promise<UpsertLessonVideoResponse> {
  return httpClient.put<UpsertLessonVideoResponse, UpsertLessonVideoRequest>(
    `admin/lessons/${lessonId}/video`,
    body,
  );
}

export function deleteAdminLessonVideo(
  lessonId: string,
): Promise<SimpleLessonActionResponse> {
  return httpClient.delete<SimpleLessonActionResponse>(
    `admin/lessons/${lessonId}/video`,
  );
}
