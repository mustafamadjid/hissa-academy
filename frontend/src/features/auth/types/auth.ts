export type UserRole = 'admin' | 'student'

export interface AuthRole {
  id: number
  name: UserRole
  guard_name: string
}

export interface AuthUser {
  id: number
  name: string
  email: string
  role: AuthRole
}

export interface LoginCredentials {
  email: string
  password: string
}
