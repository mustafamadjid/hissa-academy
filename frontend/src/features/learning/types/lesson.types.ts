export type LessonProgressStatus = "not_started" | "in_progress" | "completed";

export interface LessonProgressDto {
  id?: string;
  lesson_id?: string;
  last_position_seconds: number;
  total_watched_seconds: number;
  status: LessonProgressStatus;
  completed_at: string | null;
}

export interface LessonVideoDto {
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

export interface StudentLessonDetailDto {
  id: string;
  course_id: string;
  title: string;
  position: number;
  is_required: boolean;
  is_locked: boolean;
  video: LessonVideoDto | null;
  progress: LessonProgressDto;
}

export interface GetStudentLessonResponse {
  success: boolean;
  message: string;
  data: StudentLessonDetailDto;
}
