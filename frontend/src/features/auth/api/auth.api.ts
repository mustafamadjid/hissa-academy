import { axiosInstance } from "@/shared/api/axios";
import { httpClient } from "@/shared/api/http-client";

import type { AuthUser, LoginCredentials } from "../types/auth";

function getBackendOrigin(): string {
  const baseUrl = axiosInstance.defaults.baseURL;

  if (!baseUrl) {
    throw new Error("API base URL is not configured");
  }

  return new URL(baseUrl, window.location.origin).origin;
}

export async function getCurrentUser(): Promise<AuthUser> {
  return httpClient.get<AuthUser>("/auth/me");
}

export async function login(credentials: LoginCredentials): Promise<AuthUser> {
  await httpClient.get<void>(`${getBackendOrigin()}/sanctum/csrf-cookie`);

  await httpClient.post("/auth/login", credentials);

  return getCurrentUser();
}

export async function logout(): Promise<void> {
  await httpClient.post("/auth/logout");
}
