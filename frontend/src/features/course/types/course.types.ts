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
