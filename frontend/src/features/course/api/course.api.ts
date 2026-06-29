import { httpClient } from "@/shared/api/http-client";

import type {
  CreateCourseRequest,
  CreateCourseResponse,
  DeleteCourseResponse,
  GetAdminCoursesQuery,
  GetAdminCoursesResponse,
  UpdateCourseRequest,
  UpdateCourseResponse,
} from "../types/course.types";

const ADMIN_COURSES_ENDPOINT = "admin/courses";

export function getAdminCourses(
  query: GetAdminCoursesQuery,
): Promise<GetAdminCoursesResponse> {
  return httpClient.get<GetAdminCoursesResponse>(ADMIN_COURSES_ENDPOINT, {
    params: query,
  });
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
