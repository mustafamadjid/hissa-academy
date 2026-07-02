import { beforeEach, describe, expect, it, vi } from "vitest";

import {
  getStudentCertificateDetail,
  getStudentCertificates,
} from "../api/student-certificate.api";
import { useCourseCertificate } from "./useCourseCertificate";

vi.mock("../api/student-certificate.api", () => ({
  getStudentCertificates: vi.fn(),
  getStudentCertificateDetail: vi.fn(),
}));

const summary = {
  uuid: "certificate-id",
  certificate_number: "HISSA-2026-TEST",
  course: { uuid: "course-id", name: "Course Test" },
  issued_at: "2026-07-02T00:00:00Z",
  valid_until: "2029-07-02T00:00:00Z",
  status: "issued" as const,
};

describe("useCourseCertificate", () => {
  beforeEach(() => vi.clearAllMocks());

  it("loads the issued certificate belonging to the requested course", async () => {
    vi.mocked(getStudentCertificates).mockResolvedValue({
      success: true,
      message: "Daftar sertifikat berhasil diambil.",
      data: [summary],
      meta: { current_page: 1, per_page: 100, total: 1, last_page: 1 },
    });
    vi.mocked(getStudentCertificateDetail).mockResolvedValue({
      success: true,
      message: "Detail sertifikat berhasil diambil.",
      data: {
        ...summary,
        participant_name: "Student Test",
        verification_url: "https://example.com/verify/HISSA-2026-TEST",
        download_url:
          "https://example.com/api/v1/certificates/certificate-id/file",
      },
    });
    const { certificate, fetchCourseCertificate } = useCourseCertificate();

    await fetchCourseCertificate("course-id");

    expect(getStudentCertificateDetail).toHaveBeenCalledWith("certificate-id");
    expect(certificate.value?.download_url).toContain("certificate-id/file");
  });

  it("keeps the certificate unavailable when the course has not passed", async () => {
    vi.mocked(getStudentCertificates).mockResolvedValue({
      success: true,
      message: "Daftar sertifikat berhasil diambil.",
      data: [{ ...summary, course: { uuid: "another-course", name: "Other" } }],
      meta: { current_page: 1, per_page: 100, total: 1, last_page: 1 },
    });
    const { certificate, fetchCourseCertificate } = useCourseCertificate();

    await fetchCourseCertificate("course-id");

    expect(certificate.value).toBeNull();
    expect(getStudentCertificateDetail).not.toHaveBeenCalled();
  });
});
