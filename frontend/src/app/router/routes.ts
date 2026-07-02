import type { RouteMeta, RouteRecordRaw } from "vue-router";

const adminPlaceholderPage = () =>
  import("@/features/dashboard/pages/AdminDashboardPage.vue");

const adminRouteMeta = {
  requiresAuth: true,
  roles: ["admin"],
} satisfies RouteMeta;

const studentRouteMeta = {
  requiresAuth: true,
  roles: ["student"],
} satisfies RouteMeta;

export const routes: RouteRecordRaw[] = [
  {
    path: "/",
    name: "landing",
    component: () => import("@/features/landing/pages/LandingPage.vue"),
  },
  {
    path: "/home",
    name: "guest-home",
    component: () => import("@/features/guest/pages/HomePage.vue"),
  },
  {
    path: "/courses",
    name: "guest-courses",
    component: () => import("@/features/guest/pages/CoursesPage.vue"),
  },
  {
    path: "/courses/:courseId",
    name: "course-detail",
    component: () => import("@/features/course/Student/pages/CourseDetailPage.vue"),
  },
  {
    path: "/lessons/:lessonId",
    name: "student-lesson-detail",
    component: () => import("@/features/learning/pages/LessonDetailPage.vue"),
    meta: studentRouteMeta,
  },
  {
    path: "/courses/:courseId/quiz",
    name: "student-course-quiz",
    component: () => import("@/features/quiz/pages/StudentQuizPage.vue"),
    meta: studentRouteMeta,
  },
  {
    path: "/profile",
    name: "user-profile",
    component: () => import("@/features/profile/pages/ProfilePage.vue"),
    meta: studentRouteMeta,
  },
  {
    path: "/verify-certificate",
    name: "verify-certificate",
    component: () =>
      import("@/features/guest/pages/VerifyCertificatesPage.vue"),
  },
  {
    path: "/about",
    name: "guest-about",
    component: () => import("@/features/guest/pages/AboutPage.vue"),
  },
  {
    path: "/contact",
    name: "guest-contact",
    component: () => import("@/features/guest/pages/ContactPage.vue"),
  },
  {
    path: "/login/administrator",
    name: "administrator-login",
    component: () => import("@/features/auth/pages/LoginPage.vue"),
    meta: { guestOnly: true },
  },
  {
    path: "/login/student",
    name: "login-student",
    component: () => import("@/features/auth/pages/StudentLoginPage.vue"),
    meta: { guestOnly: true },
  },
  {
    path: "/auth/callback",
    name: "google-auth-callback",
    component: () => import("@/features/auth/pages/GoogleAuthCallbackPage.vue"),
  },
  {
    path: "/forbidden",
    name: "forbidden",
    component: () => import("@/features/auth/pages/ForbiddenPage.vue"),
  },
  {
    path: "/admin",
    name: "admin-dashboard",
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
  {
    path: "/admin/courses",
    name: "admin-courses",
    component: () =>
      import("@/features/course/Admin/pages/Admin/AdminCoursePage.vue"),
    meta: adminRouteMeta,
  },
  {
    path: "/admin/courses/:courseId",
    name: "admin-course-detail",
    component: () =>
      import("@/features/course/Admin/pages/Admin/AdminCourseDetailPage.vue"),
    meta: adminRouteMeta,
  },
  {
    path: "/admin/courses/:courseId/lessons",
    name: "admin-course-lessons",
    component: () =>
      import("@/features/course/Admin/pages/Admin/AdminCourseLessonsPage.vue"),
    meta: adminRouteMeta,
  },
  {
    path: "/admin/courses/:courseId/quiz",
    name: "admin-course-quiz",
    component: () =>
      import("@/features/quiz/pages/Admin/AdminCourseQuizPage.vue"),
    meta: adminRouteMeta,
  },
  {
    path: "/admin/students",
    name: "admin-students",
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
  {
    path: "/admin/certificates",
    name: "admin-certificates",
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
  {
    path: "/admin/settings",
    name: "admin-settings",
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
  {
    path: "/admin/help",
    name: "admin-help",
    component: adminPlaceholderPage,
    meta: adminRouteMeta,
  },
];
