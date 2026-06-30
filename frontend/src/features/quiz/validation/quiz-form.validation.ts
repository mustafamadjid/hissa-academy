import type {
  QuizFormErrors,
  QuizFormValues,
} from '../types/admin-quiz.types'

export function validateQuizForm(values: QuizFormValues): QuizFormErrors {
  const errors: QuizFormErrors = {}
  const quizName = values.quizName.trim()

  if (!quizName) {
    errors.quizName = 'Nama quiz wajib diisi.'
  } else if (quizName.length > 255) {
    errors.quizName = 'Nama quiz maksimal 255 karakter.'
  }

  return errors
}
