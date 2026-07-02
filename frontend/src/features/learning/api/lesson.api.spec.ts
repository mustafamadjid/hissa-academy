import { beforeEach, describe, expect, it, vi } from "vitest";

import { httpClient } from "@/shared/api/http-client";

import { submitLessonProgress } from "./lesson.api";

vi.mock("@/shared/api/http-client", () => ({
  httpClient: {
    get: vi.fn(),
    post: vi.fn(),
  },
}));

describe("lesson API", () => {
  beforeEach(() => vi.clearAllMocks());

  it("submits a typed progress heartbeat for the encoded lesson id", async () => {
    const payload = {
      last_position_seconds: 120,
      watched_seconds: 15,
    };
    vi.mocked(httpClient.post).mockResolvedValue({
      success: true,
      message: "Progress lesson berhasil disimpan.",
      data: {
        last_position_seconds: 120,
        total_watched_seconds: 15,
        status: "in_progress",
        completed_at: null,
      },
    });

    await submitLessonProgress("lesson/id", payload);

    expect(httpClient.post).toHaveBeenCalledWith(
      "lessons/lesson%2Fid/progress",
      payload,
    );
  });
});
