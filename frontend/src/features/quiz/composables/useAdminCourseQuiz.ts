import { readonly, ref } from 'vue'

import { ApiError } from '@/shared/api/api-error'
import {
  createAdminCourseQuiz,
  createAdminQuizQuestions,
  deleteAdminQuiz,
  deleteAdminQuizQuestion,
  getAdminCourseQuiz,
  getAdminQuizQuestions,
  reorderAdminQuizQuestions,
  updateAdminQuiz,
  updateAdminQuizQuestion,
} from '../api/admin-quiz.api'
import type {
  AdminQuizDto,
  AdminQuizQuestionDto,
  QuestionFormValues,
  QuizFormValues,
  ReorderQuizQuestionsRequest,
  UpdateQuizQuestionRequest,
} from '../types/admin-quiz.types'

function resolveError(error: unknown, fallback: string): string {
  return error instanceof ApiError ? error.message : fallback
}

function sortQuestions(
  items: readonly AdminQuizQuestionDto[],
): AdminQuizQuestionDto[] {
  return [...items].sort((first, second) => first.position - second.position)
}

export function useAdminCourseQuiz() {
  const quiz = ref<AdminQuizDto | null>(null)
  const questions = ref<AdminQuizQuestionDto[]>([])
  const isLoading = ref(false)
  const hasLoadError = ref(false)
  const isSavingQuiz = ref(false)
  const isDeletingQuiz = ref(false)
  const isSavingQuestion = ref(false)
  const isReordering = ref(false)
  const deletingQuestionId = ref<string | null>(null)
  const errorMessage = ref('')
  const successMessage = ref('')
  let requestSequence = 0

  async function loadQuestions(quizId: string): Promise<void> {
    const response = await getAdminQuizQuestions(quizId)
    questions.value = sortQuestions(response.data)
  }

  async function fetchPage(courseId: string): Promise<void> {
    const requestId = ++requestSequence
    isLoading.value = true
    hasLoadError.value = false
    errorMessage.value = ''

    try {
      const response = await getAdminCourseQuiz(courseId)
      if (requestId !== requestSequence) return

      quiz.value = response.data
      await loadQuestions(response.data.id)
    } catch (error) {
      if (requestId !== requestSequence) return

      if (error instanceof ApiError && error.statusCode === 404) {
        quiz.value = null
        questions.value = []
      } else {
        quiz.value = null
        questions.value = []
        hasLoadError.value = true
        errorMessage.value = resolveError(
          error,
          'Data quiz gagal dimuat. Silakan coba kembali.',
        )
      }
    } finally {
      if (requestId === requestSequence) isLoading.value = false
    }
  }

  async function saveQuiz(
    courseId: string,
    values: QuizFormValues,
  ): Promise<boolean> {
    if (isSavingQuiz.value) return false

    isSavingQuiz.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const body = {
        quiz_name: values.quizName.trim(),
        is_active: quiz.value?.is_active ?? true,
      }
      const response = quiz.value
        ? await updateAdminQuiz(quiz.value.id, body)
        : await createAdminCourseQuiz(courseId, body)

      quiz.value = response.data
      successMessage.value = response.message
      return true
    } catch (error) {
      errorMessage.value = resolveError(error, 'Quiz gagal disimpan.')
      return false
    } finally {
      isSavingQuiz.value = false
    }
  }

  async function removeQuiz(): Promise<boolean> {
    if (!quiz.value || isDeletingQuiz.value) return false

    isDeletingQuiz.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const response = await deleteAdminQuiz(quiz.value.id)
      quiz.value = null
      questions.value = []
      successMessage.value = response.message
      return true
    } catch (error) {
      errorMessage.value = resolveError(error, 'Quiz gagal dihapus.')
      return false
    } finally {
      isDeletingQuiz.value = false
    }
  }

  async function createQuestion(values: QuestionFormValues): Promise<boolean> {
    if (!quiz.value || isSavingQuestion.value) return false

    isSavingQuestion.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const response = await createAdminQuizQuestions(quiz.value.id, {
        questions: [
          {
            question: values.question.trim(),
            position: questions.value.length + 1,
            image_url: null,
            answers: values.answers.map((item) => ({
              answer: item.answer.trim(),
              is_correct: item.isCorrect,
            })),
          },
        ],
      })
      questions.value = sortQuestions([...questions.value, ...response.data])
      successMessage.value = response.message
      return true
    } catch (error) {
      errorMessage.value = resolveError(error, 'Pertanyaan gagal ditambahkan.')
      return false
    } finally {
      isSavingQuestion.value = false
    }
  }

  async function saveQuestion(
    question: AdminQuizQuestionDto,
    values: QuestionFormValues,
  ): Promise<boolean> {
    if (isSavingQuestion.value) return false

    isSavingQuestion.value = true
    errorMessage.value = ''
    successMessage.value = ''

    const body: UpdateQuizQuestionRequest = {
      question: values.question.trim(),
      points: question.points,
      position: question.position,
      answers: values.answers.map((item) => ({
        answer: item.answer.trim(),
        is_correct: item.isCorrect,
      })),
    }

    try {
      const response = await updateAdminQuizQuestion(question.id, body)
      questions.value = questions.value.map((item) =>
        item.id === question.id ? response.data : item,
      )
      successMessage.value = response.message
      return true
    } catch (error) {
      errorMessage.value = resolveError(error, 'Pertanyaan gagal diperbarui.')
      return false
    } finally {
      isSavingQuestion.value = false
    }
  }

  async function removeQuestion(questionId: string): Promise<boolean> {
    if (!quiz.value || deletingQuestionId.value) return false

    deletingQuestionId.value = questionId
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const response = await deleteAdminQuizQuestion(questionId)
      await loadQuestions(quiz.value.id)
      successMessage.value = response.message
      return true
    } catch (error) {
      errorMessage.value = resolveError(error, 'Pertanyaan gagal dihapus.')
      return false
    } finally {
      deletingQuestionId.value = null
    }
  }

  async function reorderQuestions(
    orderedQuestions: readonly AdminQuizQuestionDto[],
  ): Promise<boolean> {
    if (!quiz.value || isReordering.value || orderedQuestions.length === 0) {
      return false
    }

    const previousQuestions = [...questions.value]
    const nextQuestions = orderedQuestions.map((question, index) => ({
      ...question,
      position: index + 1,
    }))
    const body: ReorderQuizQuestionsRequest = {
      questions: nextQuestions.map(({ id, position }) => ({ id, position })),
    }

    questions.value = nextQuestions
    isReordering.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {
      const response = await reorderAdminQuizQuestions(quiz.value.id, body)
      questions.value = sortQuestions(response.data)
      successMessage.value = response.message
      return true
    } catch (error) {
      questions.value = previousQuestions
      errorMessage.value = resolveError(
        error,
        'Urutan pertanyaan gagal disimpan. Urutan sebelumnya dipulihkan.',
      )
      return false
    } finally {
      isReordering.value = false
    }
  }

  function clearMessages(): void {
    errorMessage.value = ''
    successMessage.value = ''
  }

  return {
    quiz: readonly(quiz),
    questions: readonly(questions),
    isLoading: readonly(isLoading),
    hasLoadError: readonly(hasLoadError),
    isSavingQuiz: readonly(isSavingQuiz),
    isDeletingQuiz: readonly(isDeletingQuiz),
    isSavingQuestion: readonly(isSavingQuestion),
    isReordering: readonly(isReordering),
    deletingQuestionId: readonly(deletingQuestionId),
    errorMessage: readonly(errorMessage),
    successMessage: readonly(successMessage),
    fetchPage,
    saveQuiz,
    removeQuiz,
    createQuestion,
    saveQuestion,
    removeQuestion,
    reorderQuestions,
    clearMessages,
  }
}
