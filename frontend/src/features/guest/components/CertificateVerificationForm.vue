<script setup lang="ts">
import { ref } from "vue";
import {
  BadgeCheck,
  LoaderCircle,
  Search,
  ShieldCheck,
} from "@lucide/vue";

interface Props {
  isLoading?: boolean;
}

withDefaults(defineProps<Props>(), {
  isLoading: false,
});

const emit = defineEmits<{
  submit: [certificateNumber: string];
}>();

const certificateNumber = ref("");

function handleSubmit(): void {
  const normalizedCertificateNumber = certificateNumber.value.trim();

  if (!normalizedCertificateNumber) {
    return;
  }

  emit("submit", normalizedCertificateNumber);
}
</script>

<template>
  <section class="bg-background px-5 py-16 sm:px-8 lg:py-24">
    <div class="mx-auto max-w-6xl">
      <div
        class="overflow-hidden rounded-3xl border border-neutral-low bg-surface shadow-sm"
      >
        <div class="grid lg:grid-cols-[0.9fr_1.1fr]">
          <!-- Information section -->
          <div
            class="relative flex flex-col justify-between bg-primary-dark-green px-6 py-10 text-white sm:px-10 sm:py-12 lg:px-12 lg:py-14"
          >
            <div>
              <div
                class="mb-8 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10"
              >
                <ShieldCheck class="h-7 w-7" aria-hidden="true" />
              </div>

              <p
                class="mb-3 text-sm font-semibold uppercase tracking-[0.18em] text-white/70"
              >
                Verifikasi Publik
              </p>

              <h1
                class="max-w-md text-3xl font-bold leading-tight sm:text-4xl"
              >
                Pastikan keaslian sertifikat HISSA Academy
              </h1>

              <p class="mt-5 max-w-lg leading-7 text-white/75">
                Gunakan nomor sertifikat yang tercantum pada dokumen untuk
                memeriksa status, pemilik, dan informasi penerbitannya.
              </p>
            </div>

            <div
              class="mt-10 flex items-start gap-3 border-t border-white/15 pt-6"
            >
              <BadgeCheck
                class="mt-0.5 h-5 w-5 shrink-0 text-white"
                aria-hidden="true"
              />

              <p class="text-sm leading-6 text-white/70">
                Hasil verifikasi ditampilkan berdasarkan data resmi yang
                tersimpan di sistem HISSA Academy.
              </p>
            </div>

            <div
              class="pointer-events-none absolute -bottom-16 -right-16 h-48 w-48 rounded-full border-[32px] border-white/5"
              aria-hidden="true"
            />
          </div>

          <!-- Form section -->
          <div class="px-6 py-10 sm:px-10 sm:py-12 lg:px-14 lg:py-14">
            <div class="mx-auto max-w-xl">
              <div class="mb-8">
                <p class="mb-2 text-sm font-semibold text-primary-green">
                  Cek sertifikat
                </p>

                <h2
                  class="text-2xl font-bold tracking-tight text-neutral-high sm:text-3xl"
                >
                  Masukkan nomor sertifikat
                </h2>

                <p class="mt-3 leading-7 text-neutral-medium">
                  Nomor sertifikat biasanya berada di bagian bawah dokumen
                  sertifikat.
                </p>
              </div>

              <form class="space-y-6" @submit.prevent="handleSubmit">
                <div>
                  <label
                    for="certificate-number"
                    class="mb-2 block text-sm font-semibold text-neutral-high"
                  >
                    Nomor sertifikat
                  </label>

                  <div class="relative">
                    <Search
                      class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-neutral-medium"
                      aria-hidden="true"
                    />

                    <input
                      id="certificate-number"
                      v-model="certificateNumber"
                      type="text"
                      name="certificate-number"
                      autocomplete="off"
                      placeholder="Contoh: HISSA-CERT-2026-001234"
                      :disabled="isLoading"
                      class="h-14 w-full rounded-xl border border-neutral-low bg-surface pl-12 pr-4 text-sm font-medium text-neutral-high outline-none transition placeholder:font-normal placeholder:text-neutral-medium/70 hover:border-primary-green/50 focus:border-primary-green focus:ring-4 focus:ring-primary-green/10 disabled:cursor-not-allowed disabled:bg-surface-dim disabled:text-neutral-medium"
                    />
                  </div>

                  <p class="mt-2 text-xs leading-5 text-neutral-medium">
                    Masukkan nomor sertifikat secara lengkap tanpa mengubah
                    susunan karakter.
                  </p>
                </div>

                <button
                  type="submit"
                  :disabled="isLoading || !certificateNumber.trim()"
                  class="inline-flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-primary-green px-6 text-sm font-semibold text-white transition hover:bg-primary-dark-green focus:outline-none focus:ring-4 focus:ring-primary-green/20 disabled:cursor-not-allowed disabled:bg-neutral-low disabled:text-neutral-medium"
                >
                  <LoaderCircle
                    v-if="isLoading"
                    class="h-5 w-5 animate-spin"
                    aria-hidden="true"
                  />

                  <Search
                    v-else
                    class="h-5 w-5"
                    aria-hidden="true"
                  />

                  <span>
                    {{
                      isLoading
                        ? "Sedang memverifikasi..."
                        : "Verifikasi sertifikat"
                    }}
                  </span>
                </button>
              </form>

              <div
                class="mt-8 rounded-2xl border border-primary-green/15 bg-primary-green/5 p-4"
              >
                <div class="flex items-start gap-3">
                  <ShieldCheck
                    class="mt-0.5 h-5 w-5 shrink-0 text-primary-green"
                    aria-hidden="true"
                  />

                  <p class="text-sm leading-6 text-neutral-medium">
                    Halaman ini hanya digunakan untuk memverifikasi sertifikat.
                    Informasi pribadi yang ditampilkan akan dibatasi sesuai
                    kebutuhan validasi.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>