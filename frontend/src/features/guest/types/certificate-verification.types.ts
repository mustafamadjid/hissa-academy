export type CertificateStatus = 'issued' | 'revoked'

export interface CertificateVerificationResponse {
  certificate_number: string
  status: CertificateStatus
  participant_name: string
  issued_at: string | null
  revoked_at: string | null
  course: {
    name: string
  }
  verification_url: string
}

export interface ApiSuccessResponse<T> {
  data: T
}
