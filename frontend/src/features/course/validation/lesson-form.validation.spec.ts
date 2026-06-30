import { describe, expect, it } from 'vitest'

import { validateLessonForm } from './lesson-form.validation'

describe('lesson form validation', () => {
  it('accepts a title without a video', () => {
    expect(
      validateLessonForm({
        title: 'Pengenalan Akad',
        videoUrl: '',
      }),
    ).toEqual({})
  })

  it('rejects a missing title and invalid video URL', () => {
    expect(
      validateLessonForm({
        title: ' ',
        videoUrl: 'https://example.com/video',
      }),
    ).toEqual({
      title: 'Judul lesson wajib diisi.',
      videoUrl: 'Masukkan URL YouTube atau video ID yang valid.',
    })
  })
})
