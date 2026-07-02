import { httpClient } from "@/shared/api/http-client";

import type {
  GetStudentCertificateDetailResponse,
  GetStudentCertificatesQuery,
  GetStudentCertificatesResponse,
} from "../types/student-certificate.types";

const CERTIFICATES_ENDPOINT = "certificates";

export function getStudentCertificates(
  query: GetStudentCertificatesQuery,
): Promise<GetStudentCertificatesResponse> {
  return httpClient.get<GetStudentCertificatesResponse>(
    CERTIFICATES_ENDPOINT,
    { params: query },
  );
}

export function getStudentCertificateDetail(
  certificateUuid: string,
): Promise<GetStudentCertificateDetailResponse> {
  return httpClient.get<GetStudentCertificateDetailResponse>(
    `${CERTIFICATES_ENDPOINT}/${encodeURIComponent(certificateUuid)}`,
  );
}
