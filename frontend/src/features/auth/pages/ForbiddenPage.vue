<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
  <main class="flex min-h-screen items-center justify-center bg-background p-6 text-center">
    <section>
      <p class="text-sm font-semibold text-error">403</p>
      <h1 class="mt-2 text-2xl font-bold text-neutral-high">Akses ditolak</h1>
      <p class="mt-2 text-sm text-neutral-medium">
        Akun Anda tidak memiliki izin untuk membuka halaman ini.
      </p>
      <button
        type="button"
        class="mt-6 rounded-lg bg-primary-dark-green px-4 py-2 text-sm font-semibold text-white"
        @click="router.back()"
      >
        Kembali
      </button>
    </section>
  </main>
</template>
