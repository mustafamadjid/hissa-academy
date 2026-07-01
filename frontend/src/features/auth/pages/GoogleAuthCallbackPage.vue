<script setup lang="ts">
import { useGoogleAuthCallback } from "../composables/useStudentGoogleAuth";

const { errorMessage, isProcessing, retry } = useGoogleAuthCallback();
</script>

<template>
  <main class="flex min-h-screen items-center justify-center bg-background px-5">
    <section class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-elevation-2" aria-live="polite">
      <div v-if="isProcessing" class="mx-auto mb-5 size-10 animate-spin rounded-full border-4 border-neutral-low border-t-primary-dark-green" aria-hidden="true" />
      <h1 class="text-xl font-bold text-neutral-high">
        {{ isProcessing ? "Menyelesaikan login..." : errorMessage ? "Login belum berhasil" : "Login berhasil" }}
      </h1>
      <p class="mt-3 text-sm leading-6 text-neutral-medium">
        {{ errorMessage || "Mohon tunggu, Anda akan diarahkan ke halaman berikutnya." }}
      </p>
      <button
        v-if="errorMessage && !isProcessing"
        type="button"
        class="mt-6 rounded-xl bg-primary-dark-green px-5 py-3 text-sm font-semibold text-white hover:bg-primary-green focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-dark-green"
        @click="retry"
      >
        Coba lagi
      </button>
    </section>
  </main>
</template>
