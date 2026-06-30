import { describe, expect, it } from 'vitest'

import { validateQuizForm } from './quiz-form.validation'

describe('quiz form validation', () => {
  it('requires a quiz name with at most 255 characters', () => {
    expect(validateQuizForm({ quizName: ' ' })).toEqual({
      quizName: 'Nama quiz wajib diisi.',
    })
    expect(validateQuizForm({ quizName: 'x'.repeat(256) })).toEqual({
      quizName: 'Nama quiz maksimal 255 karakter.',
    })
    expect(validateQuizForm({ quizName: 'Quiz Akhir' })).toEqual({})
  })
})
