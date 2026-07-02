import { createPinia, setActivePinia } from 'pinia'
import { beforeEach, describe, expect, it, vi } from 'vitest'

import { ApiError } from '@/shared/api/api-error'
import * as authApi from '../api/auth.api'
import type { AuthUser } from '../types/auth'
import { useAuthStore } from './auth.store'

vi.mock('../api/auth.api', () => ({
  getCurrentUser: vi.fn(),
  login: vi.fn(),
  logout: vi.fn(),
}))

const authenticatedUser: AuthUser = {
  email: 'john@example.com',
  full_name: 'John Doe',
  avatar_url: 'https://example.com/avatar.png',
  role: 'student',
}

describe('auth store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('restores the authenticated user only once', async () => {
    vi.mocked(authApi.getCurrentUser).mockResolvedValue(authenticatedUser)
    const store = useAuthStore()

    await store.restoreSession()
    await store.restoreSession()

    expect(store.user).toEqual(authenticatedUser)
    expect(store.isAuthenticated).toBe(true)
    expect(store.initialized).toBe(true)
    expect(store.isRestoring).toBe(false)
    expect(authApi.getCurrentUser).toHaveBeenCalledTimes(1)
  })

  it('shares one request between concurrent session restorations', async () => {
    vi.mocked(authApi.getCurrentUser).mockResolvedValue(authenticatedUser)
    const store = useAuthStore()

    await Promise.all([
      store.restoreSession(),
      store.restoreSession(),
      store.restoreSession(),
    ])

    expect(store.user).toEqual(authenticatedUser)
    expect(store.initialized).toBe(true)
    expect(store.isRestoring).toBe(false)
    expect(authApi.getCurrentUser).toHaveBeenCalledTimes(1)
  })

  it('treats an unauthorized response as an anonymous session', async () => {
    vi.mocked(authApi.getCurrentUser).mockRejectedValue(
      new ApiError('Anda belum terautentikasi.', 401),
    )
    const store = useAuthStore()

    await store.restoreSession()

    expect(store.user).toBeNull()
    expect(store.sessionError).toBeNull()
    expect(store.initialized).toBe(true)
  })

  it('keeps a recoverable error when session restoration fails', async () => {
    vi.mocked(authApi.getCurrentUser).mockRejectedValue(
      new ApiError('Tidak dapat terhubung ke server.'),
    )
    const store = useAuthStore()

    await store.restoreSession()

    expect(store.user).toBeNull()
    expect(store.sessionError).toBe('Tidak dapat terhubung ke server.')
    expect(store.initialized).toBe(true)
  })

  it('clears the authenticated user after logout', async () => {
    vi.mocked(authApi.getCurrentUser).mockResolvedValue(authenticatedUser)
    vi.mocked(authApi.logout).mockResolvedValue()
    const store = useAuthStore()
    await store.restoreSession()

    await store.signOut()

    expect(authApi.logout).toHaveBeenCalledOnce()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })
})
