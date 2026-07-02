import { computed, ref } from "vue";
import { useRouter } from "vue-router";

import { useAuthStore } from "@/features/auth/stores/auth.store";

export function useProfile() {
  const authStore = useAuthStore();
  const router = useRouter();
  const isSigningOut = ref(false);
  const logoutError = ref<string | null>(null);
  const profile = computed(() => authStore.user);

  async function logout(): Promise<void> {
    if (isSigningOut.value) return;

    isSigningOut.value = true;
    logoutError.value = null;
    const currentUser = authStore.user;

    try {
      await authStore.signOut();
      await router.replace({ name: "landing" });
    } catch {
      authStore.user = currentUser;
      logoutError.value = "Logout gagal. Silakan coba kembali.";
    } finally {
      isSigningOut.value = false;
    }
  }

  return {
    profile,
    isSigningOut,
    logoutError,
    logout,
  };
}
