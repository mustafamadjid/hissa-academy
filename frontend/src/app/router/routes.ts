import type { RouteMeta, RouteRecordRaw } from 'vue-router'

const adminPlaceholderPage = () =>
  import('@/features/dashboard/pages/AdminDashboardPage.vue')

const adminRouteMeta = {
  requiresAuth: true,
  roles: ['admin'],
} satisfies RouteMeta

export const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'landing',
    component: () => import('@/features/landing/pages/LandingPage.vue'),
  },
  {
    path: '/login/administrator',
    name: 'administrator-login',
    component: () => import('@/features/auth/pages/LoginPage.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/forbidden',
    name: 'forbidden',
    component: () => import('@/features/auth/pages/ForbiddenPage.vue'),
  },
  {
    path: '/admin',
    name: 'admin-dashboard',
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
  {
    path: '/admin/courses',
    name: 'admin-courses',
    component: () => import('@/features/course/pages/Admin/AdminCoursePage.vue'),
    meta: adminRouteMeta,
  },
  {
    path: '/admin/students',
    name: 'admin-students',
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
  {
    path: '/admin/certificates',
    name: 'admin-certificates',
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
  {
    path: '/admin/settings',
    name: 'admin-settings',
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
  {
    path: '/admin/help',
    name: 'admin-help',
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
]
