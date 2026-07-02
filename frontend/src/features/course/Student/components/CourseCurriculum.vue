<script setup lang="ts">
import { CheckCircle2, Circle, ClipboardCheck, LockKeyhole, PlayCircle } from "@lucide/vue";
import type { StudentCourseLessonDto } from "../types/course-catalog.types";

defineProps<{
  lessons: readonly StudentCourseLessonDto[];
  authenticated: boolean;
  courseId: string;
  quizUnlocked: boolean | null;
}>();
</script>

<template>
  <section aria-labelledby="course-content-title">
    <div class="mb-5 flex items-end justify-between gap-4">
      <div>
        <p
          class="text-xs font-bold uppercase tracking-[0.16em] text-primary-green"
        >
          Kurikulum
        </p>
        <h2
          id="course-content-title"
          class="mt-2 text-2xl font-bold text-neutral-high"
        >
          Konten course
        </h2>
      </div>
      <span class="text-sm text-neutral-medium"
        >{{ lessons.length }} lesson</span
      >
    </div>

    <div
      v-if="!authenticated"
      class="rounded-2xl border border-primary-green/15 bg-primary-green/5 p-6 text-sm leading-6 text-neutral-medium"
    >
      Masuk sebagai student untuk melihat daftar lesson dan progres
      pembelajaran.
    </div>
    <ol
      v-else
      class="overflow-hidden rounded-2xl border border-neutral-low bg-white shadow-sm"
    >
      <li
        v-for="lesson in lessons"
        :key="lesson.id"
        class="border-b border-neutral-low last:border-b-0"
      >
        <RouterLink
          v-if="!lesson.is_locked"
          :to="{
            name: 'student-lesson-detail',
            params: { lessonId: lesson.id },
          }"
          class="flex w-full items-center gap-4 px-5 py-4 text-left transition hover:bg-primary-green/5 focus-visible:outline-2 focus-visible:outline-primary-green disabled:cursor-not-allowed disabled:opacity-60"
        >
          <CheckCircle2
            v-if="lesson.progress?.status === 'completed'"
            class="size-5 shrink-0 text-primary-green"
          />
          <PlayCircle v-else class="size-5 shrink-0 text-primary-green" />
          <span class="min-w-0 flex-1 text-sm font-medium text-neutral-high"
            >{{ lesson.position }}. {{ lesson.title }}</span
          >
          <span class="text-xs font-semibold text-primary-green">Putar</span>
        </RouterLink>
        <div
          v-else
          class="flex items-center gap-4 bg-surface-dim/70 px-5 py-4 text-neutral-medium"
          aria-disabled="true"
        >
          <LockKeyhole class="size-5 shrink-0" />
          <span class="min-w-0 flex-1 text-sm font-medium"
            >{{ lesson.position }}. {{ lesson.title }}</span
          >
          <span class="text-xs">Selesaikan lesson sebelumnya</span>
        </div>
      </li>
      <li v-if="lessons.length > 0" class="border-t border-neutral-low">
        <RouterLink
          v-if="quizUnlocked === true"
          :to="{ name: 'student-course-quiz', params: { courseId } }"
          class="flex w-full items-center gap-4 bg-primary-green/5 px-5 py-4 text-left transition hover:bg-primary-green/10 focus-visible:outline-2 focus-visible:outline-primary-green"
        >
          <ClipboardCheck class="size-5 shrink-0 text-primary-green" />
          <span class="min-w-0 flex-1 text-sm font-bold text-neutral-high"
            >{{ lessons.length + 1 }}. Quiz Akhir Course</span
          >
          <span class="text-xs font-semibold text-primary-green">Kerjakan</span>
        </RouterLink>
        <div
          v-else-if="quizUnlocked === false"
          class="flex items-center gap-4 bg-surface-dim/70 px-5 py-4 text-neutral-medium"
          aria-disabled="true"
        >
          <LockKeyhole class="size-5 shrink-0" />
          <span class="min-w-0 flex-1 text-sm font-bold"
            >{{ lessons.length + 1 }}. Quiz Akhir Course</span
          >
          <span class="text-xs">Selesaikan semua lesson wajib</span>
        </div>
        <div
          v-else
          class="flex items-center gap-4 bg-surface-dim/70 px-5 py-4 text-neutral-medium"
          aria-disabled="true"
        >
          <LockKeyhole class="size-5 shrink-0" />
          <span class="min-w-0 flex-1 text-sm font-bold"
            >{{ lessons.length + 1 }}. Quiz Akhir Course</span
          >
          <span class="text-xs">Status akses tidak tersedia</span>
        </div>
      </li>
      <li
        v-if="lessons.length === 0"
        class="flex items-center gap-3 px-5 py-6 text-sm text-neutral-medium"
      >
        <Circle class="size-5" /> Belum ada lesson pada course ini.
      </li>
    </ol>
  </section>
</template>
