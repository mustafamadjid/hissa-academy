<script setup lang="ts">
import { ref } from "vue";
import { Eye, EyeOff } from "@lucide/vue";
import { useRoute, useRouter } from "vue-router";

import logoUrl from "@/assets/images/logo.webp";
import illustrationSrc from "@/assets/images/illustration-login.png";
import { ApiError } from "@/shared/api/api-error";
import { useAuthStore } from "../stores/auth.store";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const email = ref("");
const password = ref("");
const errorMessage = ref("");
const isSubmitting = ref(false);
const isPasswordVisible = ref(false);

function getRedirectPath(): string {
  const redirect = route.query.redirect;

  if (
    typeof redirect === "string" &&
    redirect.startsWith("/") &&
    !redirect.startsWith("//")
  ) {
    return redirect;
  }

  return "/admin/courses";
}

async function submit(): Promise<void> {
  errorMessage.value = "";
  isSubmitting.value = true;

  try {
    await authStore.signIn({
      email: email.value,
      password: password.value,
    });

    await router.replace(getRedirectPath());
  } catch (error) {
    errorMessage.value =
      error instanceof ApiError
        ? error.message
        : "Login gagal. Silakan coba kembali.";
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <main
    class="min-h-screen bg-[#F8FAFC] font-['Plus_Jakarta_Sans'] flex items-center justify-center p-4 sm:p-6 lg:p-8"
  >
    <!-- Main Card Container -->
    <div
      class="bg-white w-full max-w-[1100px] min-h-[650px] rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.05)] overflow-hidden grid lg:grid-cols-2"
    >
      <!-- Left Section: Form -->
      <section
        class="flex flex-col justify-center px-8 py-12 sm:px-12 md:px-16 lg:px-20"
      >
        <div class="w-full max-w-[400px] mx-auto lg:mx-0">
          <!-- Logo -->
          <div class="mb-12">
            <img
              :src="logoUrl"
              alt="HISSA Academy"
              width="284"
              height="159"
              class="h-10 w-auto object-contain"
              decoding="async"
            />
          </div>

          <!-- Header -->
          <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
              Welcome back
            </h1>
            <p class="text-slate-500 mt-2 text-sm leading-relaxed">
              Silakan masuk ke akun Anda untuk melanjutkan.
            </p>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="space-y-5">
            <!-- Email -->
            <div class="space-y-2">
              <label
                for="admin-email"
                class="text-sm font-semibold text-slate-700 ml-1"
                >Email
              </label>
              <div class="relative">
                <input
                  id="admin-email"
                  v-model.trim="email"
                  type="email"
                  placeholder="name@example.com"
                  required
                  class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm transition-all focus:bg-white focus:ring-4 focus:ring-primary-green/10 focus:border-primary-green outline-none"
                />
              </div>
            </div>

            <!-- Password -->
            <div class="space-y-2">
              <div class="flex justify-between items-center ml-1">
                <label
                  for="admin-password"
                  class="text-sm font-semibold text-slate-700"
                  >Password</label
                >
              </div>
              <div class="relative">
                <input
                  id="admin-password"
                  v-model="password"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  placeholder="••••••••"
                  required
                  class="w-full h-12 px-4 pr-12 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 text-sm transition-all focus:bg-white focus:ring-4 focus:ring-primary-green/10 focus:border-primary-green outline-none"
                />
                <button
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-slate-600 transition-colors"
                  @click="isPasswordVisible = !isPasswordVisible"
                >
                  <EyeOff v-if="isPasswordVisible" :size="18" />
                  <Eye v-else :size="18" />
                </button>
              </div>
            </div>

            <!-- Error Message -->
            <div
              v-if="errorMessage"
              class="bg-red-50 text-red-600 p-3 rounded-lg text-xs font-medium flex items-center gap-2"
            >
              <span class="w-1 h-1 bg-red-600 rounded-full"></span>
              {{ errorMessage }}
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full h-12 cursor-pointer mt-4 bg-primary-dark-green hover:bg-primary-green text-white rounded-xl font-bold text-sm transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed shadow-lg shadow-slate-200"
            >
              <span v-if="!isSubmitting">Login</span>
              <span v-else class="flex items-center justify-center gap-2">
                <svg
                  class="animate-spin h-4 w-4 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Processing...
              </span>
            </button>
          </form>

          <!-- Footer -->
          <p class="mt-8 text-center text-xs text-slate-400">
            &copy; 2026 HISSA Academy. All rights reserved.
          </p>
        </div>
      </section>

      <!-- Right Section: Illustration/Branding -->
      <section
        class="hidden lg:flex relative bg-slate-50 items-center justify-center p-12"
      >
        <div class="relative z-10 w-full">
          <div
            class="bg-white/40 border border-white/20 p-8 rounded-4xl shadow-xl"
          >
            <picture>
              <source media="(min-width: 1024px)" :srcset="illustrationSrc" />
              <img
                src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                alt="Dashboard Preview"
                width="800"
                height="886"
                class="w-full h-auto rounded-xl"
                loading="eager"
                fetchpriority="high"
                decoding="async"
              />
            </picture>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>
