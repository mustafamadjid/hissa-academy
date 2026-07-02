import { httpClient } from "@/shared/api/http-client";
import type {
  QuizAccessDto,
  QuizSubmitResultDto,
  StudentQuizAttemptDto,
  StudentQuizDto,
  StudentQuizResponse,
  SubmitQuizAttemptRequest,
} from "../types/student-quiz.types";

export const getStudentQuizAccess = (
  courseId: string,
): Promise<StudentQuizResponse<QuizAccessDto>> =>
  httpClient.get(`courses/${courseId}/quiz/access`);

export const getStudentCourseQuiz = (courseId: string): Promise<StudentQuizResponse<StudentQuizDto>> =>
  httpClient.get(`courses/${courseId}/quiz`);

export const createStudentQuizAttempt = (quizId: string): Promise<StudentQuizResponse<StudentQuizAttemptDto>> =>
  httpClient.post(`quizzes/${quizId}/attempts`);

export const submitStudentQuizAttempt = (
  attemptId: string,
  body: SubmitQuizAttemptRequest,
): Promise<StudentQuizResponse<QuizSubmitResultDto>> =>
  httpClient.post(`quiz-attempts/${attemptId}/submit`, body);
