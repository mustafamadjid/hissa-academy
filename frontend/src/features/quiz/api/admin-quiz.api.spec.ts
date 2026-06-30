import { beforeEach, describe, expect, it, vi } from 'vitest'

import { httpClient } from '@/shared/api/http-client'
import {
  createAdminCourseQuiz,
  createAdminQuizQuestions,
  deleteAdminQuiz,
  deleteAdminQuizQuestion,
  getAdminCourseQuiz,
  getAdminQuizQuestions,
  reorderAdminQuizQuestions,
  updateAdminQuiz,
  updateAdminQuizQuestion,
} from './admin-quiz.api'

vi.mock('@/shared/api/http-client', () => ({
  httpClient: {
    get: vi.fn(),
    post: vi.fn(),
    patch: vi.fn(),
    delete: vi.fn(),
  },
}))

describe('admin quiz API', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('uses separate quiz create, update, and delete endpoints', async () => {
    vi.mocked(httpClient.get).mockResolvedValue({})
    vi.mocked(httpClient.post).mockResolvedValue({})
    vi.mocked(httpClient.patch).mockResolvedValue({})
    vi.mocked(httpClient.delete).mockResolvedValue({})
    const body = { quiz_name: 'Quiz Akhir', is_active: true }

    await getAdminCourseQuiz('course-id')
    await createAdminCourseQuiz('course-id', body)
    await updateAdminQuiz('quiz-id', body)
    await deleteAdminQuiz('quiz-id')

    expect(httpClient.get).toHaveBeenCalledWith('admin/courses/course-id/quiz')
    expect(httpClient.post).toHaveBeenCalledWith(
      'admin/courses/course-id/quiz',
      body,
    )
    expect(httpClient.patch).toHaveBeenCalledWith(
      'admin/quizzes/quiz-id',
      body,
    )
    expect(httpClient.delete).toHaveBeenCalledWith('admin/quizzes/quiz-id')
  })

  it('uses distinct question CRUD and reorder endpoints', async () => {
    vi.mocked(httpClient.get).mockResolvedValue({})
    vi.mocked(httpClient.post).mockResolvedValue({})
    vi.mocked(httpClient.patch).mockResolvedValue({})
    vi.mocked(httpClient.delete).mockResolvedValue({})
    const answers = [{ answer: 'Benar', is_correct: true }]
    const createBody = {
      questions: [
        { question: 'Pertanyaan?', position: 1, image_url: null, answers },
      ],
    }
    const updateBody = {
      question: 'Pertanyaan baru?',
      points: 1,
      position: 1,
      answers,
    }
    const reorderBody = { questions: [{ id: 'question-id', position: 1 }] }

    await getAdminQuizQuestions('quiz-id')
    await createAdminQuizQuestions('quiz-id', createBody)
    await updateAdminQuizQuestion('question-id', updateBody)
    await reorderAdminQuizQuestions('quiz-id', reorderBody)
    await deleteAdminQuizQuestion('question-id')

    expect(httpClient.get).toHaveBeenCalledWith(
      'admin/quizzes/quiz-id/questions',
    )
    expect(httpClient.post).toHaveBeenCalledWith(
      'admin/quizzes/quiz-id/questions',
      createBody,
    )
    expect(httpClient.patch).toHaveBeenCalledWith(
      'admin/quiz-questions/question-id',
      updateBody,
    )
    expect(httpClient.patch).toHaveBeenCalledWith(
      'admin/quizzes/quiz-id/questions/reorder',
      reorderBody,
    )
    expect(httpClient.delete).toHaveBeenCalledWith(
      'admin/quiz-questions/question-id',
    )
  })
})
