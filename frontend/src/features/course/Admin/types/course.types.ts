export type CourseStatus = 'active' | 'draft' | 'inactive'

export type CourseSortBy =
  | 'course_name'
  | 'minimum_score'
  | 'status'
  | 'created_at'
  | 'updated_at'

export type SortDirection = 'asc' | 'desc'

export interface CourseDto {
  id: string
  name: string
  description: string
  minimum_score: number
  status: string
}

export interface LessonVideoDto {
  id: string
  lesson_id: string
  youtube_video_id: string | null
  video_url: string
  title: string | null
  description: string | null
  channel_title: string | null
  thumbnail_url: string | null
  duration_iso: string | null
  duration_seconds: number | null
  privacy_status: string | null
}

export interface AdminLessonDto {
  id: string
  course_id: string
  title: string
  position: number
  is_required: boolean
  video?: LessonVideoDto | null
}

export interface AdminCourseDetailDto extends CourseDto {
  total_lessons: number
  lessons: AdminLessonDto[]
}

export interface GetAdminCourseDetailResponse {
  success: boolean
  message: string
  data: AdminCourseDetailDto
}

export interface GetAdminCoursesQuery {
  page?: number
  limit?: number
  search?: string
  sort_by?: CourseSortBy
  sort_direction?: SortDirection
}

export interface CoursePaginationMeta {
  current_page: number
  per_page: number
  total: number
  last_page: number
}

export interface GetAdminCoursesResponse {
  success: boolean
  message: string
  data: CourseDto[]
  meta: CoursePaginationMeta
}

export interface CreateCourseRequest {
  course_name: string
  description: string
  minimum_score: number
  status: CourseStatus
}

export type UpdateCourseRequest = Partial<CreateCourseRequest>

export interface CreateCourseResponse {
  success: boolean
  message: string
  data: CourseDto
}

export interface UpdateCourseResponse {
  success: boolean
  message: string
  data: CourseDto
}

export interface DeleteCourseResponse {
  success: boolean
  message: string
}

export interface GetAdminCourseLessonsResponse {
  success: boolean
  message: string
  data: AdminLessonDto[]
}

export interface CreateLessonRequest {
  title: string
  youtube_video_id: string
  position: number
  is_required: boolean
}

export interface CreateLessonResponse {
  success: boolean
  message: string
  data: AdminLessonDto
}

export interface UpdateLessonRequest {
  title?: string
  position?: number
  is_required?: boolean
}

export interface UpdateLessonResponse {
  success: boolean
  message: string
  data: AdminLessonDto
}

export interface ReorderLessonItemRequest {
  id: string
  position: number
}

export interface ReorderLessonsRequest {
  lessons: ReorderLessonItemRequest[]
}

export interface SimpleLessonActionResponse {
  success: boolean
  message: string
}

export interface UpsertLessonVideoRequest {
  youtube_video_id: string
}

export interface UpsertLessonVideoResponse {
  success: boolean
  message: string
  data: LessonVideoDto
}

export interface LessonFormValues {
  title: string
  videoUrl: string
}

export interface LessonFormErrors {
  title?: string
  videoUrl?: string
}

export interface CreateLessonFormValues {
  title: string
  videoUrl: string
  isRequired: boolean
}

export interface CreateLessonFormErrors {
  title?: string
  videoUrl?: string
}

export interface CourseFormValues {
  courseName: string
  description: string
  minimumScore: number
  status: CourseStatus
}

export interface CourseFormErrors {
  courseName?: string
  description?: string
  minimumScore?: string
  status?: string
}
