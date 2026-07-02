import { axiosInstance } from "@/shared/api/axios";
import { httpClient } from "@/shared/api/http-client";

import type {
  AuthUser,
  GetCurrentUserResponse,
  LoginCredentials,
} from "../types/auth";

function getBackendOrigin(): string {
  const baseUrl = axiosInstance.defaults.baseURL;

  if (!baseUrl) {
    throw new Error("API base URL is not configured");
  }

  return new URL(baseUrl, window.location.origin).origin;
}

async function ensureCsrfCookie(): Promise<void> {
  await httpClient.get<void>(`${getBackendOrigin()}/sanctum/csrf-cookie`);
}

export function getGoogleRedirectUrl(): string {
  const baseUrl = axiosInstance.defaults.baseURL;

  if (!baseUrl) {
    throw new Error("API base URL is not configured");
  }

  return new URL(
    `${baseUrl.replace(/\/$/, "")}/auth/google/redirect`,
    window.location.origin,
  ).toString();
}

export async function getCurrentUser(): Promise<AuthUser> {
  const response = await httpClient.get<GetCurrentUserResponse>("/auth/me");

  return response.data;
}

export async function login(credentials: LoginCredentials): Promise<AuthUser> {
  await ensureCsrfCookie();

  await httpClient.post("/auth/login", credentials);

  return getCurrentUser();
}

export async function logout(): Promise<void> {
  await ensureCsrfCookie();
  await httpClient.post("/auth/logout");
}
