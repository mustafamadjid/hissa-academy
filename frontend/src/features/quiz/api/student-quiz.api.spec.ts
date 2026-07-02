import { beforeEach, describe, expect, it, vi } from "vitest";
import { httpClient } from "@/shared/api/http-client";
import { createStudentQuizAttempt, getStudentCourseQuiz, getStudentQuizAccess, submitStudentQuizAttempt } from "./student-quiz.api";

vi.mock("@/shared/api/http-client", () => ({
  httpClient: { get: vi.fn(), post: vi.fn() },
}));

describe("student quiz API", () => {
  beforeEach(() => vi.clearAllMocks());

  it("uses the student quiz attempt endpoints", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({});
    vi.mocked(httpClient.post).mockResolvedValue({});
    const body = { answers: [{ question_uuid: "question-id", selected_option_uuid: "option-id" }] };

    await getStudentCourseQuiz("course-id");
    await getStudentQuizAccess("course-id");
    await createStudentQuizAttempt("quiz-id");
    await submitStudentQuizAttempt("attempt-id", body);

    expect(httpClient.get).toHaveBeenCalledWith("courses/course-id/quiz");
    expect(httpClient.get).toHaveBeenCalledWith("courses/course-id/quiz/access");
    expect(httpClient.post).toHaveBeenNthCalledWith(1, "quizzes/quiz-id/attempts");
    expect(httpClient.post).toHaveBeenNthCalledWith(2, "quiz-attempts/attempt-id/submit", body);
  });
});
