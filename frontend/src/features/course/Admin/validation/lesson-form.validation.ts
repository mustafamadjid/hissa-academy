import type {
  LessonFormErrors,
  LessonFormValues,
} from '../types/course.types'
import { extractYoutubeVideoId } from '../utils/youtube-video'

export function validateLessonForm(values: LessonFormValues): LessonFormErrors {
  const errors: LessonFormErrors = {}
  const title = values.title.trim()
  const videoUrl = values.videoUrl.trim()

  if (!title) {
    errors.title = 'Judul lesson wajib diisi.'
  } else if (title.length > 255) {
    errors.title = 'Judul lesson maksimal 255 karakter.'
  }

  if (videoUrl && !extractYoutubeVideoId(videoUrl)) {
    errors.videoUrl = 'Masukkan URL YouTube atau video ID yang valid.'
  }

  return errors
}
