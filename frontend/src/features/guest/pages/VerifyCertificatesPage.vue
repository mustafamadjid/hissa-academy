<script setup lang="ts">
import {
  BadgeCheck,
  BookOpen,
  CalendarDays,
  CircleAlert,
  ExternalLink,
  FileBadge,
  ShieldAlert,
  UserRound,
} from "@lucide/vue";

import GuestLayout from "@/layouts/Guest/GuestLayout.vue";

import CertificateVerificationForm from "../components/CertificateVerificationForm.vue";
import { useCertificateVerification } from "../composables/useCertificateVerification";

const { data, isLoading, error, isNotFound, verify } =
  useCertificateVerification();

function handleSubmit(certificateNumber: string): void {
  verify(certificateNumber);
}

function formatDate(dateString: string | null): string {
  if (!dateString) {
    return "-";
  }

  return new Intl.DateTimeFormat("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
  }).format(new Date(dateString));
}
</script>

<template>
  <GuestLayout>
    <main class="min-h-screen bg-background">
      <CertificateVerificationForm
        :is-loading="isLoading"
        @submit="handleSubmit"
      />

      <section
        v-if="error || isNotFound || data"
        class="px-5 pb-16 sm:px-8 lg:pb-24"
      >
        <div class="mx-auto max-w-6xl">
          <!-- General error -->
          <div
            v-if="error && !isNotFound"
            class="rounded-2xl border border-red-200 bg-red-50 p-5 sm:p-6"
            role="alert"
          >
            <div class="flex items-start gap-4">
              <div
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700"
              >
                <CircleAlert class="h-5 w-5" aria-hidden="true" />
              </div>

              <div>
                <h2 class="font-semibold text-red-900">Verifikasi gagal</h2>

                <p class="mt-1 text-sm leading-6 text-red-700">
                  {{ error }}
                </p>
              </div>
            </div>
          </div>

          <!-- Not found state -->
          <div
            v-else-if="isNotFound && !isLoading"
            class="rounded-3xl border border-neutral-low bg-surface px-6 py-12 text-center shadow-sm sm:px-10"
          >
            <div
              class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-green/10 text-primary-green"
            >
              <ShieldAlert class="h-8 w-8" aria-hidden="true" />
            </div>

            <h2
              class="mt-6 text-2xl font-bold tracking-tight text-neutral-high"
            >
              Sertifikat tidak ditemukan
            </h2>

            <p class="mx-auto mt-3 max-w-xl leading-7 text-neutral-medium">
              Nomor sertifikat yang Anda masukkan tidak terdaftar dalam sistem.
              Periksa kembali susunan huruf, angka, dan tanda hubung pada nomor
              sertifikat.
            </p>

            <div
              class="mx-auto mt-6 max-w-lg rounded-2xl border border-primary-green/15 bg-primary-green/5 p-4"
            >
              <p class="text-sm leading-6 text-neutral-medium">
                Pastikan nomor sertifikat dimasukkan secara lengkap seperti yang
                tercantum pada dokumen resmi.
              </p>
            </div>
          </div>

          <!-- Verification result -->
          <article
            v-else-if="data"
            class="overflow-hidden rounded-3xl border border-neutral-low bg-surface shadow-sm"
          >
            <!-- Result header -->
            <header
              class="border-b border-neutral-low px-6 py-6 sm:px-8 lg:px-10"
            >
              <div
                class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
              >
                <div class="flex items-start gap-4">
                  <div
                    :class="[
                      'flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl',
                      data.status === 'issued'
                        ? 'bg-primary-green/10 text-primary-green'
                        : 'bg-red-100 text-red-700',
                    ]"
                  >
                    <BadgeCheck
                      v-if="data.status === 'issued'"
                      class="h-7 w-7"
                      aria-hidden="true"
                    />

                    <ShieldAlert v-else class="h-7 w-7" aria-hidden="true" />
                  </div>

                  <div>
                    <p
                      class="text-sm font-semibold uppercase tracking-[0.16em] text-neutral-medium"
                    >
                      Hasil verifikasi
                    </p>

                    <h2
                      class="mt-1 text-2xl font-bold tracking-tight text-neutral-high"
                    >
                      {{
                        data.status === "issued"
                          ? "Sertifikat terverifikasi"
                          : "Sertifikat telah dicabut"
                      }}
                    </h2>
                  </div>
                </div>

                <span
                  :class="[
                    'inline-flex w-fit items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold',
                    data.status === 'issued'
                      ? 'bg-primary-green/10 text-primary-dark-green'
                      : 'bg-red-100 text-red-800',
                  ]"
                >
                  <span
                    :class="[
                      'h-2 w-2 rounded-full',
                      data.status === 'issued'
                        ? 'bg-primary-green'
                        : 'bg-red-600',
                    ]"
                  />

                  {{ data.status === "issued" ? "Valid" : "Dicabut" }}
                </span>
              </div>

              <p
                class="mt-4 max-w-3xl text-sm leading-6 text-neutral-medium sm:ml-17"
              >
                {{
                  data.status === "issued"
                    ? "Data sertifikat sesuai dengan catatan resmi HISSA Academy."
                    : "Sertifikat ditemukan, tetapi statusnya sudah tidak berlaku."
                }}
              </p>
            </header>

            <!-- Certificate data -->
            <div class="px-6 py-8 sm:px-8 lg:px-10 lg:py-10">
              <div class="grid gap-5 md:grid-cols-2">
                <div
                  class="rounded-2xl border border-neutral-low bg-background p-5"
                >
                  <div class="flex items-start gap-4">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-green/10 text-primary-green"
                    >
                      <FileBadge class="h-5 w-5" aria-hidden="true" />
                    </div>

                    <div class="min-w-0">
                      <p class="text-sm font-medium text-neutral-medium">
                        Nomor sertifikat
                      </p>

                      <p
                        class="mt-1 wrap-break-words font-semibold text-neutral-high"
                      >
                        {{ data.certificate_number }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="rounded-2xl border border-neutral-low bg-background p-5"
                >
                  <div class="flex items-start gap-4">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-green/10 text-primary-green"
                    >
                      <UserRound class="h-5 w-5" aria-hidden="true" />
                    </div>

                    <div class="min-w-0">
                      <p class="text-sm font-medium text-neutral-medium">
                        Nama peserta
                      </p>

                      <p
                        class="mt-1 wrap-break-words font-semibold text-neutral-high"
                      >
                        {{ data.participant_name }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="rounded-2xl border border-neutral-low bg-background p-5"
                >
                  <div class="flex items-start gap-4">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-green/10 text-primary-green"
                    >
                      <BookOpen class="h-5 w-5" aria-hidden="true" />
                    </div>

                    <div class="min-w-0">
                      <p class="text-sm font-medium text-neutral-medium">
                        Kursus
                      </p>

                      <p
                        class="mt-1 wrap-break-words font-semibold text-neutral-high"
                      >
                        {{ data.course.name }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  class="rounded-2xl border border-neutral-low bg-background p-5"
                >
                  <div class="flex items-start gap-4">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-green/10 text-primary-green"
                    >
                      <CalendarDays class="h-5 w-5" aria-hidden="true" />
                    </div>

                    <div class="min-w-0">
                      <p class="text-sm font-medium text-neutral-medium">
                        Tanggal terbit
                      </p>

                      <p class="mt-1 font-semibold text-neutral-high">
                        {{ formatDate(data.issued_at) }}
                      </p>
                    </div>
                  </div>
                </div>

                <div
                  v-if="data.status === 'revoked'"
                  class="rounded-2xl border border-red-200 bg-red-50 p-5 md:col-span-2"
                >
                  <div class="flex items-start gap-4">
                    <div
                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700"
                    >
                      <CalendarDays class="h-5 w-5" aria-hidden="true" />
                    </div>

                    <div>
                      <p class="text-sm font-medium text-red-700">
                        Tanggal pencabutan
                      </p>

                      <p class="mt-1 font-semibold text-red-900">
                        {{ formatDate(data.revoked_at) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action -->
              <div
                class="mt-8 flex flex-col gap-4 border-t border-neutral-low pt-8 sm:flex-row sm:items-center sm:justify-between"
              >
                <p class="max-w-xl text-sm leading-6 text-neutral-medium">
                  Buka halaman sertifikat untuk melihat informasi lengkap dan
                  dokumen terkait.
                </p>

                <a
                  :href="data.verification_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-primary-green px-5 text-sm font-semibold text-white transition hover:bg-primary-dark-green focus:outline-none focus:ring-4 focus:ring-primary-green/20"
                >
                  <span>Lihat sertifikat lengkap</span>
                  <ExternalLink class="h-4 w-4" aria-hidden="true" />
                </a>
              </div>
            </div>
          </article>
        </div>
      </section>
    </main>
  </GuestLayout>
</template>
