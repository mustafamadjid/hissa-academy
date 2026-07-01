import { computed, ref } from 'vue'
import { defineStore } from 'pinia'

import { ApiError } from '@/shared/api/api-error'
import * as authApi from '../api/auth.api'
import type { AuthUser, LoginCredentials } from '../types/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<AuthUser | null>(null)
  const initialized = ref(false)
  const isRestoring = ref(false)
  const sessionError = ref<string | null>(null)
  const isAuthenticated = computed(() => user.value !== null)
  let restorePromise: Promise<void> | null = null

  function restoreSession(): Promise<void> {
    if (initialized.value) {
      return Promise.resolve()
    }

    if (restorePromise) {
      return restorePromise
    }

    restorePromise = (async () => {
      isRestoring.value = true
      sessionError.value = null

      try {
        user.value = await authApi.getCurrentUser()
      } catch (error) {
        user.value = null

        if (!(error instanceof ApiError && error.statusCode === 401)) {
          sessionError.value =
            error instanceof ApiError
              ? error.message
              : 'Gagal memulihkan sesi. Silakan coba kembali.'
        }
      } finally {
        initialized.value = true
        isRestoring.value = false
        restorePromise = null
      }
    })()

    return restorePromise
  }

  async function signIn(credentials: LoginCredentials): Promise<void> {
    sessionError.value = null
    user.value = await authApi.login(credentials)
    initialized.value = true
  }

  async function signOut(): Promise<void> {
    try {
      await authApi.logout()
    } finally {
      user.value = null
      initialized.value = true
      sessionError.value = null
    }
  }

  return {
    user,
    initialized,
    isRestoring,
    sessionError,
    isAuthenticated,
    restoreSession,
    signIn,
    signOut,
  }
})
