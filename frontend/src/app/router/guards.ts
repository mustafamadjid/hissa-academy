import type { Router } from "vue-router";

import { pinia } from "@/app/pinia";
import { useAuthStore } from "@/features/auth/stores/auth.store";

export function registerRouterGuards(router: Router): void {
  router.beforeEach(async (to) => {
    const authStore = useAuthStore(pinia);

    await authStore.restoreSession();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      if (to.meta.roles?.includes("student")) {
        return {
          name: "login-student",
          query: { redirect: to.fullPath },
        };
      }

      return { name: "landing" };
    }

    if (to.meta.guestOnly && authStore.isAuthenticated) {
      return authStore.user?.role === "admin"
        ? { name: "admin-courses" }
        : { name: "landing" };
    }

    if (
      to.meta.roles?.length &&
      (!authStore.user || !to.meta.roles.includes(authStore.user.role))
    ) {
      return { name: "landing" };
    }

    return true;
  });
}
