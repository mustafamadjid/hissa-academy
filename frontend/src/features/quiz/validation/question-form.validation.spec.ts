import { describe, expect, it } from 'vitest'

import { validateQuestionForm } from './question-form.validation'

describe('question form validation', () => {
  it('accepts a question with options and one answer key', () => {
    expect(
      validateQuestionForm({
        question: 'Apa akad yang digunakan?',
        answers: [
          { answer: 'Murabahah', isCorrect: true },
          { answer: 'Deposito', isCorrect: false },
        ],
      }),
    ).toEqual({})
  })

  it('rejects blank fields and a missing answer key', () => {
    expect(
      validateQuestionForm({
        question: ' ',
        answers: [{ answer: ' ', isCorrect: false }],
      }),
    ).toEqual({
      question: 'Pertanyaan wajib diisi.',
      answers: 'Pilih satu opsi sebagai kunci jawaban.',
      answerItems: ['Opsi jawaban wajib diisi.'],
    })
  })
})
