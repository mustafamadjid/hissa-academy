import { beforeEach, describe, expect, it, vi } from "vitest";

import { getStudentQuizAccess } from "../api/student-quiz.api";
import { useQuizAccess } from "./useQuizAccess";

vi.mock("../api/student-quiz.api", () => ({
  getStudentQuizAccess: vi.fn(),
}));

describe("useQuizAccess", () => {
  beforeEach(() => vi.clearAllMocks());

  it("uses the server response as the quiz access state", async () => {
    vi.mocked(getStudentQuizAccess).mockResolvedValue({
      success: true,
      message: "Akses quiz berhasil diperiksa.",
      data: {
        course_uuid: "course-id",
        quiz_uuid: "quiz-id",
        can_access: false,
        quizPassed: false,
        required_lessons: 2,
        completed_required_lessons: 1,
        reason: "REQUIRED_LESSONS_NOT_COMPLETED",
      },
    });
    const { quizAccess, quizAccessError, fetchQuizAccess } = useQuizAccess();

    await fetchQuizAccess("course-id");

    expect(getStudentQuizAccess).toHaveBeenCalledWith("course-id");
    expect(quizAccess.value?.can_access).toBe(false);
    expect(quizAccess.value?.quizPassed).toBe(false);
    expect(quizAccess.value?.completed_required_lessons).toBe(1);
    expect(quizAccessError.value).toBeNull();
  });

  it("does not infer access when the server request fails", async () => {
    vi.mocked(getStudentQuizAccess).mockRejectedValue(
      new Error("Status akses tidak tersedia."),
    );
    const { quizAccess, quizAccessError, fetchQuizAccess } = useQuizAccess();

    await fetchQuizAccess("course-id");

    expect(quizAccess.value).toBeNull();
    expect(quizAccessError.value).toBe("Status akses tidak tersedia.");
  });
});
