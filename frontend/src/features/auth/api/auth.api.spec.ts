import { beforeEach, describe, expect, it, vi } from 'vitest'

import { httpClient } from '@/shared/api/http-client'
import type { AuthUser } from '../types/auth'
import { getCurrentUser } from './auth.api'

vi.mock('@/shared/api/http-client', () => ({
  httpClient: {
    get: vi.fn(),
    post: vi.fn(),
  },
}))

const authenticatedUser: AuthUser = {
  id: 1,
  name: 'John Doe',
  email: 'john@example.com',
  role: {
    id: 2,
    name: 'student',
    guard_name: 'web',
  },
}

describe('auth API', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('gets the authenticated user and role from auth/me', async () => {
    vi.mocked(httpClient.get).mockResolvedValue(authenticatedUser)

    await expect(getCurrentUser()).resolves.toEqual(authenticatedUser)
    expect(httpClient.get).toHaveBeenCalledWith('/auth/me')
  })
})
