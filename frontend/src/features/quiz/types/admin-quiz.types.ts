export interface AdminQuizDto {
  id: string
  course_id: string
  quiz_name: string
  is_active: boolean
}

export interface AdminQuizAnswerDto {
  id: string
  answer: string
  is_correct: boolean
}

export interface AdminQuizQuestionDto {
  id: string
  quizz_id: string
  question: string
  points: number
  position: number
  image_url: string | null
  answers: AdminQuizAnswerDto[]
}

export interface UpsertQuizRequest {
  quiz_name: string
  is_active: boolean
}

export interface AdminQuizResponse {
  success: boolean
  message: string
  data: AdminQuizDto
}

export interface AdminQuizAnswerRequest {
  answer: string
  is_correct: boolean
}

export interface CreateQuizQuestionRequest {
  quizz_id?: string | null
  question: string
  position: number
  image_url?: string | null
  answers: AdminQuizAnswerRequest[]
}

export interface CreateQuizQuestionsRequest {
  questions: CreateQuizQuestionRequest[]
}

export interface ReorderQuizQuestionItemRequest {
  id: string
  position: number
}

export interface ReorderQuizQuestionsRequest {
  questions: ReorderQuizQuestionItemRequest[]
}

export interface UpdateQuizQuestionRequest {
  question: string
  points: number
  position: number
  answers: AdminQuizAnswerRequest[]
}

export interface AdminQuestionListResponse {
  success: boolean
  message: string
  data: AdminQuizQuestionDto[]
}

export interface AdminQuestionResponse {
  success: boolean
  message: string
  data: AdminQuizQuestionDto
}

export interface SimpleQuizActionResponse {
  success: boolean
  message: string
}

export interface QuizFormValues {
  quizName: string
}

export interface QuizFormErrors {
  quizName?: string
}

export interface QuestionAnswerFormValue {
  answer: string
  isCorrect: boolean
}

export interface QuestionFormValues {
  question: string
  answers: QuestionAnswerFormValue[]
}

export interface QuestionFormErrors {
  question?: string
  answers?: string
  answerItems?: Array<string | undefined>
}
