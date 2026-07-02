<script setup lang="ts">
import { computed, ref } from "vue";
import { LogOut, Mail, UserRound } from "@lucide/vue";

import GuestLayout from "@/layouts/Guest/GuestLayout.vue";
import { useProfile } from "../composables/useProfile";

const { profile, isSigningOut, logoutError, logout } = useProfile();
const hasAvatarError = ref(false);
const initial = computed(
  () => profile.value?.full_name.trim().charAt(0).toUpperCase() || "U",
);
</script>

<template>
  <GuestLayout>
    <section class="min-h-[calc(100vh-5rem)] bg-background px-5 py-12 sm:px-8 sm:py-16">
      <div class="mx-auto max-w-3xl">
        <div class="mb-8">
          <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-primary-green">
            Akun Saya
          </p>
          <h1 class="text-3xl font-bold tracking-tight text-neutral-high sm:text-4xl">
            Profil Pengguna
          </h1>
          <p class="mt-3 text-neutral-medium">
            Informasi akun yang digunakan untuk mengakses HISSA Academy.
          </p>
        </div>

        <div v-if="profile" class="overflow-hidden rounded-2xl border border-neutral-low bg-white shadow-elevation-2">
          <div class="h-28 bg-gradient-to-r from-primary-dark-green to-primary-green sm:h-36" />

          <div class="px-6 pb-8 sm:px-10">
            <div class="-mt-14 mb-6 flex size-28 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-surface-dim text-3xl font-bold text-primary-dark-green shadow-md">
              <img
                v-if="profile.avatar_url && !hasAvatarError"
                :src="profile.avatar_url"
                :alt="`Avatar ${profile.full_name}`"
                class="size-full object-cover"
                @error="hasAvatarError = true"
              />
              <span v-else aria-hidden="true">{{ initial }}</span>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
              <div class="rounded-xl bg-surface-dim p-4">
                <div class="mb-2 flex items-center gap-2 text-sm font-medium text-neutral-medium">
                  <UserRound class="size-4" aria-hidden="true" />
                  Nama lengkap
                </div>
                <p class="font-semibold text-neutral-high">{{ profile.full_name }}</p>
              </div>

              <div class="rounded-xl bg-surface-dim p-4">
                <div class="mb-2 flex items-center gap-2 text-sm font-medium text-neutral-medium">
                  <Mail class="size-4" aria-hidden="true" />
                  Email
                </div>
                <p class="break-all font-semibold text-neutral-high">{{ profile.email }}</p>
              </div>
            </div>

            <div class="mt-8 border-t border-neutral-low pt-6">
              <p v-if="logoutError" role="alert" class="mb-4 text-sm font-medium text-error">
                {{ logoutError }}
              </p>
              <button
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-error px-5 py-3 text-sm font-semibold text-error transition-colors hover:bg-error hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="isSigningOut"
                :aria-busy="isSigningOut"
                @click="logout"
              >
                <LogOut class="size-4" aria-hidden="true" />
                {{ isSigningOut ? "Sedang logout..." : "Logout" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </GuestLayout>
</template>
