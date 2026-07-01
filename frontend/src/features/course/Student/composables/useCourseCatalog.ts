import { computed, onBeforeUnmount, onMounted, readonly, ref } from "vue";

import { getCourseCatalog } from "../api/course-catalog.api";
import type { StudentCourseSummaryDto } from "../types/course-catalog.types";

const PAGE_SIZE = 6;
const SEARCH_DELAY_MS = 350;

export function useCourseCatalog() {
  const courses = ref<StudentCourseSummaryDto[]>([]);
  const search = ref("");
  const page = ref(1);
  const isLoading = ref(false);
  const error = ref<string | null>(null);
  let requestId = 0;
  let searchTimer: ReturnType<typeof setTimeout> | null = null;

  const canGoBack = computed(() => page.value > 1);
  const canGoForward = computed(() => courses.value.length === PAGE_SIZE);

  async function fetchCourses(): Promise<void> {
    const activeRequestId = ++requestId;
    isLoading.value = true;
    error.value = null;

    try {
      const response = await getCourseCatalog({
        page: page.value,
        limit: PAGE_SIZE,
        search: search.value.trim() || undefined,
        sort_by: "created_at",
        sort_direction: "desc",
      });

      if (activeRequestId === requestId) {
        courses.value = response.data;
      }
    } catch (caughtError: unknown) {
      if (activeRequestId === requestId) {
        courses.value = [];
        error.value =
          caughtError instanceof Error
            ? caughtError.message
            : "Daftar course gagal dimuat.";
      }
    } finally {
      if (activeRequestId === requestId) {
        isLoading.value = false;
      }
    }
  }

  function updateSearch(value: string): void {
    search.value = value;
    page.value = 1;

    if (searchTimer) clearTimeout(searchTimer);
    searchTimer = setTimeout(() => void fetchCourses(), SEARCH_DELAY_MS);
  }

  function goToPreviousPage(): void {
    if (!canGoBack.value) return;
    page.value -= 1;
    void fetchCourses();
  }

  function goToNextPage(): void {
    if (!canGoForward.value) return;
    page.value += 1;
    void fetchCourses();
  }

  onMounted(() => void fetchCourses());
  onBeforeUnmount(() => {
    requestId += 1;
    if (searchTimer) clearTimeout(searchTimer);
  });

  return {
    courses: readonly(courses),
    search: readonly(search),
    page: readonly(page),
    isLoading: readonly(isLoading),
    error: readonly(error),
    canGoBack,
    canGoForward,
    fetchCourses,
    updateSearch,
    goToPreviousPage,
    goToNextPage,
  };
}
