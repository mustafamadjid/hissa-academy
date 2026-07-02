import { ref, readonly } from 'vue'
import type { CertificateVerificationResponse } from '../types/certificate-verification.types'
import { verifyCertificate } from '../api/certificate.api'
import type { ApiError } from '@/shared/api/api-error'

export function useCertificateVerification() {
  const data = ref<CertificateVerificationResponse | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const isNotFound = ref(false)

  async function verify(certificateNumber: string): Promise<void> {
    if (!certificateNumber.trim()) {
      error.value = 'Nomor sertifikat harus diisi.'
      return
    }

    isLoading.value = true
    error.value = null
    isNotFound.value = false
    data.value = null

    try {
      const result = await verifyCertificate(certificateNumber)
      data.value = result
    } catch (err) {
      const apiError = err as ApiError
      if (apiError.statusCode === 404) {
        isNotFound.value = true
        error.value = 'Sertifikat tidak ditemukan.'
      } else {
        error.value = apiError.message || 'Gagal memverifikasi sertifikat.'
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    data: readonly(data),
    isLoading: readonly(isLoading),
    error: readonly(error),
    isNotFound: readonly(isNotFound),
    verify,
  }
}
