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
