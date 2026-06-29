import { describe, expect, it } from 'vitest'

import { validateCourseForm } from './course-form.validation'

describe('course form validation', () => {
  it('accepts valid course values', () => {
    expect(
      validateCourseForm({
        courseName: 'Dasar Perbankan Syariah',
        description: 'Pengenalan konsep perbankan syariah.',
        minimumScore: 75,
        status: 'draft',
      }),
    ).toEqual({})
  })

  it('rejects empty fields and a score outside the backend range', () => {
    expect(
      validateCourseForm({
        courseName: ' ',
        description: '',
        minimumScore: 101,
        status: 'active',
      }),
    ).toEqual({
      courseName: 'Nama course wajib diisi.',
      description: 'Deskripsi wajib diisi.',
      minimumScore: 'Nilai kelulusan harus di antara 0 dan 100.',
    })
  })
})
