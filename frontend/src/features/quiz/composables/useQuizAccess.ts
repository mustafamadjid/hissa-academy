import { readonly, ref } from "vue";

import { getStudentQuizAccess } from "../api/student-quiz.api";
import type { QuizAccessDto } from "../types/student-quiz.types";

export function useQuizAccess() {
  const quizAccess = ref<QuizAccessDto | null>(null);
  const isQuizAccessLoading = ref(false);
  const quizAccessError = ref<string | null>(null);

  async function fetchQuizAccess(courseId: string): Promise<void> {
    if (isQuizAccessLoading.value) return;

    isQuizAccessLoading.value = true;
    quizAccessError.value = null;
    quizAccess.value = null;

    try {
      const response = await getStudentQuizAccess(courseId);
      quizAccess.value = response.data;
    } catch (caughtError: unknown) {
      quizAccessError.value =
        caughtError instanceof Error
          ? caughtError.message
          : "Status akses quiz gagal dimuat.";
    } finally {
      isQuizAccessLoading.value = false;
    }
  }

  return {
    quizAccess: readonly(quizAccess),
    isQuizAccessLoading: readonly(isQuizAccessLoading),
    quizAccessError: readonly(quizAccessError),
    fetchQuizAccess,
  };
}
