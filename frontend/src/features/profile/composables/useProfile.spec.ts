import { createPinia, setActivePinia } from "pinia";
import { createMemoryHistory, createRouter } from "vue-router";
import { beforeEach, describe, expect, it, vi } from "vitest";

import { useAuthStore } from "@/features/auth/stores/auth.store";
import { useProfile } from "./useProfile";

const router = createRouter({
  history: createMemoryHistory(),
  routes: [
    { path: "/", name: "landing", component: { template: "<div />" } },
    { path: "/profile", name: "user-profile", component: { template: "<div />" } },
  ],
});

vi.mock("vue-router", async (importOriginal) => {
  const actual = await importOriginal<typeof import("vue-router")>();

  return {
    ...actual,
    useRouter: () => router,
  };
});

describe("useProfile", () => {
  beforeEach(async () => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
    await router.push({ name: "user-profile" });
  });

  it("logs out once and redirects to the landing page", async () => {
    const authStore = useAuthStore();
    const signOut = vi.spyOn(authStore, "signOut").mockResolvedValue();
    const { logout } = useProfile();

    await logout();

    expect(signOut).toHaveBeenCalledOnce();
    expect(router.currentRoute.value.name).toBe("landing");
  });

});
