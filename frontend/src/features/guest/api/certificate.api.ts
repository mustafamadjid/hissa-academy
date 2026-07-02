import { httpClient } from "@/shared/api/http-client";
import type {
  CertificateVerificationResponse,
  ApiSuccessResponse,
} from "../types/certificate-verification.types";

export async function verifyCertificate(
  certificateNumber: string,
): Promise<CertificateVerificationResponse> {
  const response = await httpClient.get<
    ApiSuccessResponse<CertificateVerificationResponse>
  >(`public/certificates/${certificateNumber}`);

  return response.data;
}
