<script setup lang="ts">
import logoUrl from "@/assets/images/logo.webp";
import illustrationSrc from "@/assets/images/illustration-login.png";

import GoogleSignInButton from "../components/GoogleSignInButton.vue";
import { useStudentGoogleLogin } from "../composables/useStudentGoogleAuth";

const { errorMessage, isRedirecting, signInWithGoogle } =
  useStudentGoogleLogin();
</script>

<template>
  <main class="flex min-h-screen items-center justify-center bg-background p-4 sm:p-6 lg:p-8">
    <div class="grid min-h-[640px] w-full max-w-6xl overflow-hidden rounded-3xl bg-white shadow-elevation-3 lg:grid-cols-2">
      <section class="flex flex-col justify-center px-7 py-10 sm:px-14 lg:px-20" aria-labelledby="student-login-title">
        <div class="mx-auto w-full max-w-md lg:mx-0">
          <RouterLink :to="{ name: 'guest-home' }" aria-label="Kembali ke beranda HISSA Academy">
            <img :src="logoUrl" alt="HISSA Academy" class="mb-12 h-12 w-auto object-contain" />
          </RouterLink>

          <p class="mb-3 text-sm font-semibold text-primary-green">Portal Student</p>
          <h1 id="student-login-title" class="text-3xl font-bold tracking-tight text-neutral-high sm:text-4xl">
            Mulai perjalanan belajarmu
          </h1>
          <p class="mt-4 text-base leading-7 text-neutral-medium">
            Masuk atau daftar dengan akun Google untuk mengakses course dan melanjutkan progres belajar.
          </p>

          <div class="mt-9">
            <GoogleSignInButton :loading="isRedirecting" @click="signInWithGoogle" />
            <p v-if="errorMessage" class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-error" role="alert">
              {{ errorMessage }}
            </p>
          </div>

          <p class="mt-7 text-xs leading-5 text-neutral-medium">
            Dengan melanjutkan, Anda menyetujui ketentuan layanan dan kebijakan privasi HISSA Academy.
          </p>
        </div>
      </section>

      <section class="relative hidden items-center justify-center overflow-hidden bg-primary-dark-green p-12 lg:flex" aria-label="Ilustrasi pembelajaran HISSA Academy">
        <div class="absolute -right-24 -top-24 size-72 rounded-full bg-lime-accent/15" />
        <div class="absolute -bottom-28 -left-20 size-80 rounded-full bg-primary-green/50" />
        <div class="relative z-10">
          <div class="rounded-3xl bg-white/10 p-7 shadow-2xl ring-1 ring-white/20 backdrop-blur-sm">
            <img :src="illustrationSrc" alt="Tampilan platform pembelajaran HISSA Academy" class="w-full rounded-2xl bg-white" />
          </div>
          <h2 class="mt-8 text-center text-2xl font-bold text-white">Belajar fleksibel, berkembang nyata.</h2>
          <p class="mx-auto mt-3 max-w-md text-center text-sm leading-6 text-white/75">Akses materi terstruktur, kuis, dan sertifikat dalam satu platform.</p>
        </div>
      </section>
    </div>
  </main>
</template>
