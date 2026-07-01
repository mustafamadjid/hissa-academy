<template>
  <section class="bg-primary-dark-green px-5 py-20 sm:px-8 lg:py-28">
    <div class="relative mx-auto max-w-6xl overflow-hidden rounded-[40px] bg-primary-green px-6 py-16 text-center shadow-2xl sm:px-12 lg:py-20">
      <div class="absolute -right-20 -top-28 h-80 w-80 rotate-45 bg-white/10" />
      <div class="relative mx-auto max-w-3xl">
        <h2 class="text-3xl font-bold leading-tight text-white sm:text-5xl">Siap Memulai Perjalanan Keuangan Etis Anda?</h2>
        <p class="mx-auto mt-6 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">Bergabunglah dengan ribuan siswa yang telah mengubah pemahaman mereka tentang pasar saham melalui kurikulum kami yang sesuai Syariah.</p>
        <div class="mt-9 flex flex-wrap justify-center gap-4">
          <RouterLink :to="{ name: 'guest-courses' }" class="rounded-xl bg-lime-accent px-8 py-4 font-bold text-primary-dark-green transition hover:scale-105">Daftar Sekarang</RouterLink>
          <RouterLink :to="{ name: 'guest-contact' }" class="rounded-xl border border-white/25 bg-white/10 px-8 py-4 font-bold text-white backdrop-blur transition hover:bg-white/20">Bicara dengan Mentor</RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>
