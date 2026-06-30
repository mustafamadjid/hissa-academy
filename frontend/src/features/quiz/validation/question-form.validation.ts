import type {
  QuestionFormErrors,
  QuestionFormValues,
} from '../types/admin-quiz.types'

export function validateQuestionForm(
  values: QuestionFormValues,
): QuestionFormErrors {
  const errors: QuestionFormErrors = {}
  const question = values.question.trim()

  if (!question) {
    errors.question = 'Pertanyaan wajib diisi.'
  } else if (question.length > 255) {
    errors.question = 'Pertanyaan maksimal 255 karakter.'
  }

  if (values.answers.length === 0) {
    errors.answers = 'Tambahkan minimal satu opsi jawaban.'
    return errors
  }

  const answerItems = values.answers.map((item) => {
    const answer = item.answer.trim()
    if (!answer) return 'Opsi jawaban wajib diisi.'
    if (answer.length > 255) return 'Opsi jawaban maksimal 255 karakter.'
    return undefined
  })

  if (answerItems.some(Boolean)) errors.answerItems = answerItems

  if (!values.answers.some((item) => item.isCorrect)) {
    errors.answers = 'Pilih satu opsi sebagai kunci jawaban.'
  }

  return errors
}
