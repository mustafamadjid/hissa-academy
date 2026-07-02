import { beforeEach, describe, expect, it, vi } from 'vitest'

import { httpClient } from '@/shared/api/http-client'
import type { AuthUser } from '../types/auth'
import { getCurrentUser, logout } from './auth.api'

vi.mock('@/shared/api/http-client', () => ({
  httpClient: {
    get: vi.fn(),
    post: vi.fn(),
  },
}))

const authenticatedUser: AuthUser = {
  email: 'john@example.com',
  full_name: 'John Doe',
  avatar_url: 'https://example.com/avatar.png',
  role: 'student',
}

describe('auth API', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('gets the authenticated user and role from auth/me', async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      success: true,
      message: 'Profil user berhasil diambil.',
      data: authenticatedUser,
    })

    await expect(getCurrentUser()).resolves.toEqual(authenticatedUser)
    expect(httpClient.get).toHaveBeenCalledWith('/auth/me')
  })

  it('ends the authenticated session through auth/logout', async () => {
    vi.mocked(httpClient.get).mockResolvedValue(undefined)
    vi.mocked(httpClient.post).mockResolvedValue(undefined)

    await expect(logout()).resolves.toBeUndefined()
    expect(httpClient.get).toHaveBeenCalledWith(
      'http://localhost:8000/sanctum/csrf-cookie',
    )
    expect(httpClient.post).toHaveBeenCalledWith('/auth/logout')
  })
})
