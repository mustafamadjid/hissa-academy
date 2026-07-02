import { beforeEach, describe, expect, it, vi } from "vitest";

import { httpClient } from "@/shared/api/http-client";

import {
  getStudentCertificateDetail,
  getStudentCertificates,
} from "./student-certificate.api";

vi.mock("@/shared/api/http-client", () => ({
  httpClient: { get: vi.fn() },
}));

describe("student certificate API", () => {
  beforeEach(() => vi.clearAllMocks());

  it("uses the student certificate list and detail endpoints", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({});

    await getStudentCertificates({ page: 1, limit: 100 });
    await getStudentCertificateDetail("certificate/id");

    expect(httpClient.get).toHaveBeenNthCalledWith(1, "certificates", {
      params: { page: 1, limit: 100 },
    });
    expect(httpClient.get).toHaveBeenNthCalledWith(
      2,
      "certificates/certificate%2Fid",
    );
  });
});
