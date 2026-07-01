<script setup lang="ts">
import { ArrowLeft, BookOpen, CheckCircle2, Clock3, LoaderCircle, LockKeyhole, PlayCircle } from "@lucide/vue";
import { computed, onMounted } from "vue";
import { useRoute } from "vue-router";

import GuestLayout from "@/layouts/Guest/GuestLayout.vue";

import { useLessonDetail } from "../composables/useLessonDetail";

const route = useRoute();
const lessonId = computed(() => String(route.params.lessonId));
const { lesson, isLoading, error, isLocked, embedUrl, fetchLesson } = useLessonDetail();

function formatDuration(seconds: number | null): string {
  if (!seconds) return "Durasi belum tersedia";
  const minutes = Math.floor(seconds / 60);
  const remainder = seconds % 60;
  return `${minutes}:${String(remainder).padStart(2, "0")}`;
}

onMounted(() => void fetchLesson(lessonId.value));
</script>

<template>
  <GuestLayout>
    <main class="min-h-screen bg-background px-5 py-10 sm:px-8">
      <div v-if="isLoading" class="grid min-h-[60vh] place-items-center"><LoaderCircle class="size-10 animate-spin text-primary-green" /></div>

      <section v-else-if="error || !lesson" class="mx-auto grid min-h-[60vh] max-w-xl place-items-center text-center">
        <div>
          <div class="mx-auto grid size-16 place-items-center rounded-2xl bg-primary-green/10"><LockKeyhole v-if="isLocked" class="size-8 text-primary-green" /><BookOpen v-else class="size-8 text-primary-green" /></div>
          <h1 class="mt-5 text-2xl font-bold text-neutral-high">{{ isLocked ? 'Lesson masih terkunci' : 'Lesson tidak tersedia' }}</h1>
          <p class="mt-3 leading-7 text-neutral-medium">{{ error }}</p>
          <RouterLink :to="{ name: 'guest-courses' }" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-primary-dark-green px-5 py-3 text-sm font-bold text-white"><ArrowLeft class="size-4" /> Kembali ke course</RouterLink>
        </div>
      </section>

      <div v-else class="mx-auto max-w-6xl">
        <RouterLink :to="{ name: 'course-detail', params: { courseId: lesson.course_id } }" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-green"><ArrowLeft class="size-4" /> Kembali ke detail course</RouterLink>
        <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_300px]">
          <article>
            <div class="aspect-video overflow-hidden rounded-2xl bg-primary-dark-green shadow-elevation-3">
              <iframe v-if="embedUrl" :src="embedUrl" :title="lesson.video?.title ?? lesson.title" class="h-full w-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen />
              <div v-else class="grid h-full place-items-center text-center text-white"><div><PlayCircle class="mx-auto size-16 text-lime-accent" /><p class="mt-4 font-semibold">Video lesson belum tersedia</p></div></div>
            </div>
            <p class="mt-8 text-xs font-bold uppercase tracking-[0.16em] text-primary-green">Lesson {{ lesson.position }}</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-primary-dark-green sm:text-4xl">{{ lesson.title }}</h1>
            <p v-if="lesson.video?.description" class="mt-5 whitespace-pre-line text-sm leading-7 text-neutral-medium">{{ lesson.video.description }}</p>
          </article>

          <aside class="h-fit rounded-2xl border border-neutral-low bg-white p-6 shadow-sm">
            <h2 class="font-bold text-neutral-high">Detail lesson</h2>
            <ul class="mt-5 space-y-4 text-sm text-neutral-medium">
              <li class="flex items-center gap-3"><Clock3 class="size-5 text-primary-green" /> {{ formatDuration(lesson.video?.duration_seconds ?? null) }}</li>
              <li class="flex items-center gap-3"><CheckCircle2 class="size-5 text-primary-green" /> {{ lesson.progress.status === 'completed' ? 'Sudah selesai' : 'Belum selesai' }}</li>
              <li class="flex items-center gap-3"><BookOpen class="size-5 text-primary-green" /> {{ lesson.is_required ? 'Lesson wajib' : 'Lesson opsional' }}</li>
            </ul>
            <div class="mt-6 h-2 overflow-hidden rounded-full bg-surface-dim"><div class="h-full rounded-full bg-primary-green" :style="{ width: lesson.progress.status === 'completed' ? '100%' : '0%' }" /></div>
          </aside>
        </div>
      </div>
    </main>
  </GuestLayout>
</template>
