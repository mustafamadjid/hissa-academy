import type {
  CourseFormErrors,
  CourseFormValues,
} from '../types/course.types'

export function validateCourseForm(values: CourseFormValues): CourseFormErrors {
  const errors: CourseFormErrors = {}
  const courseName = values.courseName.trim()
  const description = values.description.trim()

  if (!courseName) {
    errors.courseName = 'Nama course wajib diisi.'
  } else if (courseName.length > 255) {
    errors.courseName = 'Nama course maksimal 255 karakter.'
  }

  if (!description) {
    errors.description = 'Deskripsi wajib diisi.'
  } else if (description.length > 255) {
    errors.description = 'Deskripsi maksimal 255 karakter.'
  }

  if (!Number.isFinite(values.minimumScore)) {
    errors.minimumScore = 'Nilai kelulusan wajib berupa angka.'
  } else if (values.minimumScore < 0 || values.minimumScore > 100) {
    errors.minimumScore = 'Nilai kelulusan harus di antara 0 dan 100.'
  }

  if (!['active', 'draft', 'inactive'].includes(values.status)) {
    errors.status = 'Status course tidak valid.'
  }

  return errors
}
