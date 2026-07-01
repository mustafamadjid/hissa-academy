export type CourseCatalogSortBy =
  | "name"
  | "minimum_score"
  | "created_at"
  | "updated_at";

export type CourseCatalogSortDirection = "asc" | "desc";

export interface GetCourseCatalogQuery {
  page?: number;
  limit?: number;
  search?: string;
  sort_by?: CourseCatalogSortBy;
  sort_direction?: CourseCatalogSortDirection;
}

export interface StudentCourseSummaryDto {
  id: string;
  name: string;
  description: string;
  minimum_score: number;
  status: string;
  total_lessons: number;
  completed_lessons: number;
  progress_percentage: number;
}

export interface GetCourseCatalogResponse {
  success: boolean;
  message: string;
  data: StudentCourseSummaryDto[];
}

export type LessonProgressStatus = "not_started" | "in_progress" | "completed";

export interface StudentLessonProgressDto {
  id?: string;
  lesson_id?: string;
  last_position_seconds: number;
  total_watched_seconds: number;
  status: LessonProgressStatus;
  completed_at: string | null;
}

export interface StudentLessonVideoDto {
  id: string;
  lesson_id: string;
  youtube_video_id: string | null;
  video_url: string;
  title: string | null;
  description: string | null;
  channel_title: string | null;
  thumbnail_url: string | null;
  duration_iso: string | null;
  duration_seconds: number | null;
  privacy_status: string | null;
}

export interface StudentCourseLessonDto {
  id: string;
  course_id: string;
  title: string;
  position: number;
  is_required: boolean;
  is_locked: boolean;
  video: StudentLessonVideoDto | null;
  progress: StudentLessonProgressDto | null;
}

export interface StudentCourseDetailDto extends StudentCourseSummaryDto {
  lessons: StudentCourseLessonDto[];
}

export interface GetStudentCourseDetailResponse {
  success: boolean;
  message: string;
  data: StudentCourseDetailDto;
}
