import { readonly, ref } from "vue";

import {
  getStudentCertificateDetail,
  getStudentCertificates,
} from "../api/student-certificate.api";
import type { StudentCertificateDetailDto } from "../types/student-certificate.types";

export function useCourseCertificate() {
  const certificate = ref<StudentCertificateDetailDto | null>(null);
  const isCertificateLoading = ref(false);
  const certificateError = ref<string | null>(null);

  async function fetchCourseCertificate(courseId: string): Promise<void> {
    if (isCertificateLoading.value) return;

    isCertificateLoading.value = true;
    certificateError.value = null;
    certificate.value = null;

    try {
      let currentPage = 1;
      let lastPage = 1;
      let certificateUuid: string | null = null;

      do {
        const listResponse = await getStudentCertificates({
          page: currentPage,
          limit: 100,
        });
        const summary = listResponse.data.find(
          (item) => item.course.uuid === courseId && item.status === "issued",
        );

        certificateUuid = summary?.uuid ?? null;
        lastPage = listResponse.meta.last_page;
        currentPage += 1;
      } while (certificateUuid === null && currentPage <= lastPage);

      if (certificateUuid) {
        certificate.value = (
          await getStudentCertificateDetail(certificateUuid)
        ).data;
      }
    } catch (caughtError: unknown) {
      certificateError.value =
        caughtError instanceof Error
          ? caughtError.message
          : "Sertifikat gagal dimuat.";
    } finally {
      isCertificateLoading.value = false;
    }
  }

  return {
    certificate: readonly(certificate),
    isCertificateLoading: readonly(isCertificateLoading),
    certificateError: readonly(certificateError),
    fetchCourseCertificate,
  };
}
