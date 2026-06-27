import axios from "axios";

import type { ApiErrorResponse, ApiValidationErrors } from "./api.type";

export class ApiError extends Error {
  public readonly statusCode: number | null;
  public readonly validationErrors: ApiValidationErrors | null;

  public constructor(
    message: string,
    statusCode: number | null = null,
    validationErrors: ApiValidationErrors | null = null,
  ) {
    super(message);

    this.name = "ApiError";
    this.statusCode = statusCode;
    this.validationErrors = validationErrors;
  }
}

export function normalizeApiError(error: unknown): ApiError {
  if (!axios.isAxiosError<ApiErrorResponse>(error)) {
    if (error instanceof Error) {
      return new ApiError(error.message);
    }

    return new ApiError("Terjadi kesalahan yang tidak diketahui.");
  }

  if (!error.response) {
    if (error.code === "ECONNABORTED") {
      return new ApiError("Request melebihi batas waktu.");
    }

    return new ApiError(
      "Tidak dapat terhubung ke server. Periksa koneksi internet Anda.",
    );
  }

  const statusCode = error.response.status;
  const responseData = error.response.data;

  return new ApiError(
    responseData?.message ?? getDefaultErrorMessage(statusCode),
    statusCode,
    responseData?.errors ?? null,
  );
}

function getDefaultErrorMessage(statusCode: number): string {
  switch (statusCode) {
    case 400:
      return "Request tidak valid.";

    case 401:
      return "Anda belum terautentikasi.";

    case 403:
      return "Anda tidak memiliki izin untuk melakukan tindakan ini.";

    case 404:
      return "Resource tidak ditemukan.";

    case 409:
      return "Terjadi konflik pada data.";

    case 422:
      return "Data yang dikirim tidak valid.";

    case 429:
      return "Terlalu banyak request. Silakan coba kembali.";

    case 500:
      return "Terjadi kesalahan pada server.";

    default:
      return "Request gagal diproses.";
  }
}
