export type CertificateStatus = "issued" | "revoked";

export interface CertificateCourseDto {
  uuid: string;
  name: string;
}

export interface StudentCertificateSummaryDto {
  uuid: string;
  certificate_number: string;
  course: CertificateCourseDto;
  issued_at: string;
  valid_until: string;
  status: CertificateStatus;
}

export interface StudentCertificateDetailDto
  extends StudentCertificateSummaryDto {
  participant_name: string;
  verification_url: string;
  download_url: string;
}

export interface CertificatePaginationMeta {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
}

export interface GetStudentCertificatesQuery {
  page: number;
  limit: number;
}

export interface GetStudentCertificatesResponse {
  success: boolean;
  message: string;
  data: StudentCertificateSummaryDto[];
  meta: CertificatePaginationMeta;
}

export interface GetStudentCertificateDetailResponse {
  success: boolean;
  message: string;
  data: StudentCertificateDetailDto;
}
