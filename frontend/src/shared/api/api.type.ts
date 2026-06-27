export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface ApiCollectionResponse<T> {
  success: boolean;
  message: string;
  data: T[];
}

export interface PaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  per_page: number;
  to: number | null;
  total: number;
}

export interface PaginatedData<T> {
  items: T[];
  meta: PaginationMeta;
}

export interface ApiValidationErrors {
  [field: string]: string[];
}

export interface ApiErrorResponse {
  message: string;
  errors?: ApiValidationErrors;
}
