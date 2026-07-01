import { httpClient } from "@/shared/api/http-client";

import type {
  GetCourseCatalogQuery,
  GetCourseCatalogResponse,
} from "../types/course-catalog.types";

const COURSE_CATALOG_ENDPOINT = "courses";

export function getCourseCatalog(
  query: GetCourseCatalogQuery,
): Promise<GetCourseCatalogResponse> {
  return httpClient.get<GetCourseCatalogResponse>(COURSE_CATALOG_ENDPOINT, {
    params: query,
  });
}
