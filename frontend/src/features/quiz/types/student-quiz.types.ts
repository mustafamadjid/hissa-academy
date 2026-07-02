export interface StudentQuizAttemptPolicyDto {
  maximum_attempts: number | null;
  attempts_used: number;
  attempts_remaining: number | null;
}

export interface QuizAccessDto {
  course_uuid: string;
  quiz_uuid: string;
  can_access: boolean;
  required_lessons: number;
  completed_required_lessons: number;
  reason: "REQUIRED_LESSONS_NOT_COMPLETED" | null;
}

export interface StudentQuizDto {
  uuid: string;
  name: string;
  is_active: boolean;
  minimum_score: number;
  total_questions: number;
  attempt_policy: StudentQuizAttemptPolicyDto;
}

export type QuizAttemptStatus = "in_progress" | "submitted" | "passed" | "failed";

export interface StudentQuizOptionDto {
  uuid: string;
  option_text: string;
  position: number;
}

export interface StudentQuizQuestionDto {
  uuid: string;
  question_text: string;
  points: number;
  position: number;
  selected_option_uuid: string | null;
  options: StudentQuizOptionDto[];
}

export interface StudentQuizAttemptDto {
  uuid: string;
  quiz: { uuid: string; name: string };
  status: QuizAttemptStatus;
  score: number | null;
  started_at: string;
  submitted_at: string | null;
  questions: StudentQuizQuestionDto[];
}

export interface SubmitQuizAttemptRequest {
  answers: Array<{ question_uuid: string; selected_option_uuid: string }>;
}

export interface QuizSubmitResultDto {
  attempt_uuid: string;
  score: number;
  minimum_score: number;
  status: "passed" | "failed";
  started_at: string;
  submitted_at: string;
  result: { correct_answers: number; incorrect_answers: number; total_questions: number };
  certificate: {
    uuid: string;
    certificate_number: string;
    status: "issued" | "revoked";
    issued_at: string;
    valid_until: string;
    file_url: string;
  } | null;
}

export interface StudentQuizResponse<T> {
  success: boolean;
  message: string;
  data: T;
}
