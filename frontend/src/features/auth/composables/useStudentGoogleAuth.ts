import { onMounted, readonly, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useAuthStore } from "../stores/auth.store";
import { getGoogleRedirectUrl } from "../api/auth.api";

const REDIRECT_STORAGE_KEY = "hissa.student-login.redirect";
const DEFAULT_REDIRECT_PATH = "/courses";

function isSafeRedirectPath(value: unknown): value is string {
  return (
    typeof value === "string" &&
    value.startsWith("/") &&
    !value.startsWith("//")
  );
}

function getRequestedRedirect(value: unknown): string {
  return isSafeRedirectPath(value) ? value : DEFAULT_REDIRECT_PATH;
}

export function useStudentGoogleLogin() {
  const route = useRoute();
  const errorMessage = ref("");
  const isRedirecting = ref(false);

  function signInWithGoogle(): void {
    if (isRedirecting.value) return;

    errorMessage.value = "";
    isRedirecting.value = true;

    try {
      const redirectPath = getRequestedRedirect(route.query.redirect);
      sessionStorage.setItem(REDIRECT_STORAGE_KEY, redirectPath);
      window.location.assign(getGoogleRedirectUrl());
    } catch {
      isRedirecting.value = false;
      errorMessage.value =
        "Tidak dapat membuka login Google. Silakan coba kembali.";
    }
  }

  return {
    errorMessage: readonly(errorMessage),
    isRedirecting: readonly(isRedirecting),
    signInWithGoogle,
  };
}

export function useGoogleAuthCallback() {
  const router = useRouter();
  const authStore = useAuthStore();
  const errorMessage = ref("");
  const isProcessing = ref(true);

  async function completeLogin(): Promise<void> {
    errorMessage.value = "";
    isProcessing.value = true;

    try {
      await authStore.restoreSession();

      if (!authStore.user) {
        errorMessage.value =
          authStore.sessionError ??
          "Sesi Google tidak berhasil dibuat. Silakan login kembali.";
        return;
      }

      if (authStore.user.role !== "student") {
        await router.replace(
          authStore.user.role === "admin"
            ? { name: "admin-courses" }
            : { name: "forbidden" },
        );
        return;
      }

      const storedRedirect = sessionStorage.getItem(REDIRECT_STORAGE_KEY);
      sessionStorage.removeItem(REDIRECT_STORAGE_KEY);
      await router.replace(getRequestedRedirect(storedRedirect));
    } finally {
      isProcessing.value = false;
    }
  }

  onMounted(() => {
    void completeLogin();
  });

  return {
    errorMessage: readonly(errorMessage),
    isProcessing: readonly(isProcessing),
    retry: completeLogin,
  };
}
