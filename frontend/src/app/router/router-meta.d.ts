import type { UserRole } from '@/features/auth/types/auth'

import 'vue-router'

export {}

declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
    guestOnly?: boolean
    roles?: UserRole[]
  }
}
