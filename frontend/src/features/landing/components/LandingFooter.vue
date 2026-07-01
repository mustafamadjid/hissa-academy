<script setup lang="ts">
import { Globe2, MessageCircle, Users } from '@lucide/vue'

const footerGroups = [
  { title: 'Eksplorasi', links: [{ label: 'Kursus', route: 'guest-courses' }, { label: 'Tentang Kami', route: 'guest-about' }] },
  { title: 'Layanan', links: [{ label: 'Verifikasi Sertifikat', route: 'guest-certificates' }, { label: 'Hubungi Kami', route: 'guest-contact' }] },
]
</script>

<template>
  <footer class="bg-primary-dark-green text-white">
    <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 sm:px-8 md:grid-cols-2 lg:grid-cols-4 lg:py-24">
      <div class="md:col-span-2">
        <h2 class="text-2xl font-bold text-lime-accent">HISSA Academy</h2>
        <p class="mt-5 max-w-md leading-7 text-white/65">Memberdayakan umat dengan pendidikan keuangan etis kelas dunia yang berakar pada prinsip-prinsip Syariah.</p>
        <div class="mt-6 flex gap-3">
          <span v-for="icon in [Globe2, Users, MessageCircle]" :key="icon.name" class="rounded-full bg-white/10 p-3"><component :is="icon" :size="19" /></span>
        </div>
      </div>
      <div v-for="group in footerGroups" :key="group.title">
        <h3 class="text-sm font-bold uppercase tracking-wider">{{ group.title }}</h3>
        <ul class="mt-5 space-y-4">
          <li v-for="link in group.links" :key="link.route"><RouterLink :to="{ name: link.route }" class="text-white/65 transition hover:text-lime-accent">{{ link.label }}</RouterLink></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-white/10 px-5 py-7 text-center text-sm text-white/50">© {{ new Date().getFullYear() }} HISSA Academy. Keunggulan dalam Keuangan Syariah.</div>
  </footer>
</template>
