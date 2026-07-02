import { computed, readonly, ref } from "vue";
import { createStudentQuizAttempt, getStudentCourseQuiz, submitStudentQuizAttempt } from "../api/student-quiz.api";
import type { QuizSubmitResultDto, StudentQuizAttemptDto, StudentQuizDto } from "../types/student-quiz.types";

export function useStudentQuiz() {
  const quiz = ref<StudentQuizDto | null>(null);
  const attempt = ref<StudentQuizAttemptDto | null>(null);
  const result = ref<QuizSubmitResultDto | null>(null);
  const answers = ref<Record<string, string>>({});
  const isLoading = ref(false);
  const isSubmitting = ref(false);
  const error = ref<string | null>(null);
  const isLocked = ref(false);
  const allAnswered = computed(() =>
    Boolean(attempt.value?.questions.length) &&
    attempt.value!.questions.every((question) => Boolean(answers.value[question.uuid])),
  );

  async function loadQuiz(courseId: string): Promise<void> {
    isLoading.value = true;
    error.value = null;
    isLocked.value = false;
    attempt.value = null;
    result.value = null;
    answers.value = {};
    try {
      quiz.value = (await getStudentCourseQuiz(courseId)).data;
    } catch (caught: unknown) {
      error.value = caught instanceof Error ? caught.message : "Quiz gagal dimuat.";
      isLocked.value = typeof caught === "object" && caught !== null && "statusCode" in caught && caught.statusCode === 403;
    } finally {
      isLoading.value = false;
    }
  }

  async function startAttempt(): Promise<void> {
    if (!quiz.value || isLoading.value) return;
    isLoading.value = true;
    error.value = null;
    try {
      attempt.value = (await createStudentQuizAttempt(quiz.value.uuid)).data;
      answers.value = Object.fromEntries(
        attempt.value.questions.filter((q) => q.selected_option_uuid).map((q) => [q.uuid, q.selected_option_uuid!]),
      );
    } catch (caught: unknown) {
      error.value = caught instanceof Error ? caught.message : "Quiz tidak dapat dimulai.";
    } finally {
      isLoading.value = false;
    }
  }

  function selectAnswer(questionId: string, optionId: string): void {
    answers.value = { ...answers.value, [questionId]: optionId };
  }

  async function submitAttempt(): Promise<void> {
    if (!attempt.value || !allAnswered.value || isSubmitting.value) return;
    isSubmitting.value = true;
    error.value = null;
    try {
      result.value = (await submitStudentQuizAttempt(attempt.value.uuid, {
        answers: attempt.value.questions.map((question) => ({
          question_uuid: question.uuid,
          selected_option_uuid: answers.value[question.uuid]!,
        })),
      })).data;
    } catch (caught: unknown) {
      error.value = caught instanceof Error ? caught.message : "Jawaban quiz gagal dikirim.";
    } finally {
      isSubmitting.value = false;
    }
  }

  return { quiz: readonly(quiz), attempt: readonly(attempt), result: readonly(result), answers: readonly(answers), isLoading: readonly(isLoading), isSubmitting: readonly(isSubmitting), error: readonly(error), isLocked: readonly(isLocked), allAnswered, loadQuiz, startAttempt, selectAnswer, submitAttempt };
}
