import { beforeEach, describe, expect, it, vi } from "vitest";

import {
  getStudentCourseQuiz,
  getStudentQuizAccess,
} from "../api/student-quiz.api";
import { useStudentQuiz } from "./useStudentQuiz";

vi.mock("../api/student-quiz.api", () => ({
  createStudentQuizAttempt: vi.fn(),
  getStudentCourseQuiz: vi.fn(),
  getStudentQuizAccess: vi.fn(),
  submitStudentQuizAttempt: vi.fn(),
}));

describe("useStudentQuiz", () => {
  beforeEach(() => vi.clearAllMocks());

  it("loads the quiz only when access is allowed and it has not been passed", async () => {
    vi.mocked(getStudentQuizAccess).mockResolvedValue({
      success: true,
      message: "Akses quiz berhasil diperiksa.",
      data: {
        course_uuid: "course-id",
        quiz_uuid: "quiz-id",
        can_access: true,
        quizPassed: false,
        required_lessons: 1,
        completed_required_lessons: 1,
        reason: null,
      },
    });
    vi.mocked(getStudentCourseQuiz).mockResolvedValue({
      success: true,
      message: "Quiz berhasil diambil.",
      data: {
        uuid: "quiz-id",
        name: "Quiz akhir",
        is_active: true,
        minimum_score: 75,
        total_questions: 1,
        attempt_policy: {
          maximum_attempts: null,
          attempts_used: 0,
          attempts_remaining: null,
        },
      },
    });
    const { quiz, loadQuiz } = useStudentQuiz();

    await loadQuiz("course-id");

    expect(getStudentCourseQuiz).toHaveBeenCalledWith("course-id");
    expect(quiz.value?.uuid).toBe("quiz-id");
  });

  it("does not load the quiz when it has already been passed", async () => {
    vi.mocked(getStudentQuizAccess).mockResolvedValue({
      success: true,
      message: "Akses quiz berhasil diperiksa.",
      data: {
        course_uuid: "course-id",
        quiz_uuid: "quiz-id",
        can_access: false,
        quizPassed: true,
        required_lessons: 1,
        completed_required_lessons: 1,
        reason: "QUIZ_ALREADY_PASSED",
      },
    });
    const { isLocked, lockReason, loadQuiz } = useStudentQuiz();

    await loadQuiz("course-id");

    expect(getStudentCourseQuiz).not.toHaveBeenCalled();
    expect(isLocked.value).toBe(true);
    expect(lockReason.value).toBe("QUIZ_ALREADY_PASSED");
  });
});
