export type UserRole = 'admin' | 'student' | 'user'

export interface AuthUser {
  email: string
  full_name: string
  avatar_url: string | null
  role: UserRole
}

export interface GetCurrentUserResponse {
  success: true
  message: string
  data: AuthUser
}

export interface LoginCredentials {
  email: string
  password: string
}
