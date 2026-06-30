import type {
  CreateLessonFormErrors,
  CreateLessonFormValues,
} from '../types/course.types'
import { extractYoutubeVideoId } from '../utils/youtube-video'

export function validateCreateLessonForm(
  values: CreateLessonFormValues,
): CreateLessonFormErrors {
  const errors: CreateLessonFormErrors = {}
  const title = values.title.trim()
  const videoUrl = values.videoUrl.trim()

  if (!title) {
    errors.title = 'Judul lesson wajib diisi.'
  } else if (title.length > 255) {
    errors.title = 'Judul lesson maksimal 255 karakter.'
  }

  if (!videoUrl) {
    errors.videoUrl = 'Link video YouTube wajib diisi.'
  } else if (!extractYoutubeVideoId(videoUrl)) {
    errors.videoUrl = 'Masukkan URL YouTube yang valid.'
  }

  return errors
}
