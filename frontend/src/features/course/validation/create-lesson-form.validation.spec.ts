import { describe, expect, it } from 'vitest'

import { validateCreateLessonForm } from './create-lesson-form.validation'

describe('create lesson form validation', () => {
  it('accepts complete lesson values', () => {
    expect(
      validateCreateLessonForm({
        title: 'Pengenalan Akad',
        videoUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        isRequired: true,
      }),
    ).toEqual({})
  })

  it('requires a title and video URL according to the create contract', () => {
    expect(
      validateCreateLessonForm({
        title: ' ',
        videoUrl: '',
        isRequired: false,
      }),
    ).toEqual({
      title: 'Judul lesson wajib diisi.',
      videoUrl: 'Link video YouTube wajib diisi.',
    })
  })
})
