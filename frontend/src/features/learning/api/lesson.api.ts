import { httpClient } from "@/shared/api/http-client";

import type { GetStudentLessonResponse } from "../types/lesson.types";

export function getStudentLesson(lessonId: string): Promise<GetStudentLessonResponse> {
  return httpClient.get<GetStudentLessonResponse>(
    `lessons/${encodeURIComponent(lessonId)}`,
  );
}
