import { httpClient } from '@/shared/api/http-client'

import type {
  AdminQuestionListResponse,
  AdminQuestionResponse,
  AdminQuizResponse,
  CreateQuizQuestionsRequest,
  ReorderQuizQuestionsRequest,
  SimpleQuizActionResponse,
  UpdateQuizQuestionRequest,
  UpsertQuizRequest,
} from '../types/admin-quiz.types'

const ADMIN_COURSES_ENDPOINT = 'admin/courses'
const ADMIN_QUIZZES_ENDPOINT = 'admin/quizzes'
const ADMIN_QUESTIONS_ENDPOINT = 'admin/quiz-questions'

export function getAdminCourseQuiz(courseId: string): Promise<AdminQuizResponse> {
  return httpClient.get<AdminQuizResponse>(
    `${ADMIN_COURSES_ENDPOINT}/${courseId}/quiz`,
  )
}

export function createAdminCourseQuiz(
  courseId: string,
  body: UpsertQuizRequest,
): Promise<AdminQuizResponse> {
  return httpClient.post<AdminQuizResponse, UpsertQuizRequest>(
    `${ADMIN_COURSES_ENDPOINT}/${courseId}/quiz`,
    body,
  )
}

export function updateAdminQuiz(
  quizId: string,
  body: UpsertQuizRequest,
): Promise<AdminQuizResponse> {
  return httpClient.patch<AdminQuizResponse, UpsertQuizRequest>(
    `${ADMIN_QUIZZES_ENDPOINT}/${quizId}`,
    body,
  )
}

export function deleteAdminQuiz(
  quizId: string,
): Promise<SimpleQuizActionResponse> {
  return httpClient.delete<SimpleQuizActionResponse>(
    `${ADMIN_QUIZZES_ENDPOINT}/${quizId}`,
  )
}

export function getAdminQuizQuestions(
  quizId: string,
): Promise<AdminQuestionListResponse> {
  return httpClient.get<AdminQuestionListResponse>(
    `${ADMIN_QUIZZES_ENDPOINT}/${quizId}/questions`,
  )
}

export function createAdminQuizQuestions(
  quizId: string,
  body: CreateQuizQuestionsRequest,
): Promise<AdminQuestionListResponse> {
  return httpClient.post<AdminQuestionListResponse, CreateQuizQuestionsRequest>(
    `${ADMIN_QUIZZES_ENDPOINT}/${quizId}/questions`,
    body,
  )
}

export function reorderAdminQuizQuestions(
  quizId: string,
  body: ReorderQuizQuestionsRequest,
): Promise<AdminQuestionListResponse> {
  return httpClient.patch<
    AdminQuestionListResponse,
    ReorderQuizQuestionsRequest
  >(`${ADMIN_QUIZZES_ENDPOINT}/${quizId}/questions/reorder`, body)
}

export function updateAdminQuizQuestion(
  questionId: string,
  body: UpdateQuizQuestionRequest,
): Promise<AdminQuestionResponse> {
  return httpClient.patch<AdminQuestionResponse, UpdateQuizQuestionRequest>(
    `${ADMIN_QUESTIONS_ENDPOINT}/${questionId}`,
    body,
  )
}

export function deleteAdminQuizQuestion(
  questionId: string,
): Promise<SimpleQuizActionResponse> {
  return httpClient.delete<SimpleQuizActionResponse>(
    `${ADMIN_QUESTIONS_ENDPOINT}/${questionId}`,
  )
}
