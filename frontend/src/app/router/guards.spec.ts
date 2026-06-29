import { createMemoryHistory, createRouter, type RouteRecordRaw } from 'vue-router'
import { beforeEach, describe, expect, it, vi } from 'vitest'

import { useAuthStore } from '@/features/auth/stores/auth.store'
import type { AuthUser } from '@/features/auth/types/auth'
import { registerRouterGuards } from './guards'

vi.mock('@/features/auth/stores/auth.store', () => ({
  useAuthStore: vi.fn(),
}))

const adminUser: AuthUser = {
  id: 1,
  name: 'Admin',
  email: 'admin@example.com',
  role: { id: 1, name: 'admin', guard_name: 'web' },
}

const studentUser: AuthUser = {
  ...adminUser,
  id: 2,
  name: 'Student',
  role: { id: 2, name: 'student', guard_name: 'web' },
}

const testRoutes: RouteRecordRaw[] = [
  { path: '/', name: 'landing', component: { template: '<div />' } },
  {
    path: '/login/administrator',
    name: 'administrator-login',
    component: { template: '<div />' },
    meta: { guestOnly: true },
  },
  { path: '/forbidden', name: 'forbidden', component: { template: '<div />' } },
  {
    path: '/admin',
    name: 'admin-courses',
    component: { template: '<div />' },
    meta: { requiresAuth: true, roles: ['admin'] },
  },
]

function createGuardedRouter() {
  const router = createRouter({
    history: createMemoryHistory(),
    routes: testRoutes,
  })

  registerRouterGuards(router)

  return router
}

function mockAuth(user: AuthUser | null): void {
  vi.mocked(useAuthStore).mockReturnValue({
    user,
    isAuthenticated: user !== null,
    restoreSession: vi.fn().mockResolvedValue(undefined),
  } as unknown as ReturnType<typeof useAuthStore>)
}

describe('router guards', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('redirects unauthenticated users to the landing page', async () => {
    mockAuth(null)
    const router = createGuardedRouter()

    await router.push('/admin')

    expect(router.currentRoute.value.name).toBe('landing')
  })

  it('allows unauthenticated users to open the administrator login page', async () => {
    mockAuth(null)
    const router = createGuardedRouter()

    await router.push('/login/administrator')

    expect(router.currentRoute.value.name).toBe('administrator-login')
  })

  it('redirects authenticated users without the required role', async () => {
    mockAuth(studentUser)
    const router = createGuardedRouter()

    await router.push('/admin')

    expect(router.currentRoute.value.name).toBe('forbidden')
  })

  it('allows authenticated users with the required role', async () => {
    mockAuth(adminUser)
    const router = createGuardedRouter()

    await router.push('/admin')

    expect(router.currentRoute.value.name).toBe('admin-courses')
  })
})
