<script setup lang="ts">
import { CheckCircle2, ClipboardCheck, LockKeyhole, PlayCircle } from "@lucide/vue";

import type { StudentLessonDetailDto } from "../types/lesson.types";

defineProps<{
  lessons: readonly StudentLessonDetailDto[];
  activeLessonId: string;
  courseId: string;
  quizUnlocked: boolean | null;
}>();
</script>

<template>
  <aside
    class="flex min-h-0 flex-col bg-white lg:border-l lg:border-neutral-low"
  >
    <div class="border-b border-neutral-low px-5 py-4">
      <p
        class="text-xs font-bold uppercase tracking-[0.14em] text-primary-green"
      >
        Materi course
      </p>
      <h2 class="mt-1 text-lg font-bold text-neutral-high">Urutan Lesson</h2>
    </div>

    <ol class="overflow-y-auto">
      <li
        v-for="item in lessons"
        :key="item.id"
        class="border-b border-neutral-low last:border-b-0"
      >
        <div
          v-if="item.is_locked"
          class="flex items-center gap-3 bg-surface-dim/70 px-5 py-4 text-neutral-medium"
          aria-disabled="true"
        >
          <LockKeyhole class="size-5 shrink-0" />
          <span class="min-w-0 flex-1 text-sm font-medium"
            >{{ item.position }}. {{ item.title }}</span
          >
        </div>
        <RouterLink
          v-else
          :to="{ name: 'student-lesson-detail', params: { lessonId: item.id } }"
          class="flex items-center gap-3 px-5 py-4 transition hover:bg-primary-green/5 focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-primary-green"
          :class="
            item.id === activeLessonId ? 'bg-primary-green/10' : 'bg-white'
          "
          :aria-current="item.id === activeLessonId ? 'page' : undefined"
        >
          <CheckCircle2
            v-if="item.progress?.status === 'completed'"
            class="size-5 shrink-0 text-success"
          />
          <PlayCircle v-else class="size-5 shrink-0 text-primary-green" />
          <span class="min-w-0 flex-1 text-sm font-semibold text-neutral-high"
            >{{ item.position }}. {{ item.title }}</span
          >
        </RouterLink>
      </li>
      <li class="border-t border-neutral-low">
        <RouterLink
          v-if="quizUnlocked === true"
          :to="{ name: 'student-course-quiz', params: { courseId } }"
          class="flex items-center gap-3 bg-primary-green/5 px-5 py-4 transition hover:bg-primary-green/10 focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-primary-green"
        >
          <ClipboardCheck class="size-5 shrink-0 text-primary-green" />
          <span class="min-w-0 flex-1 text-sm font-bold text-neutral-high"
            >{{ lessons.length + 1 }}. Quiz Akhir Course</span
          >
        </RouterLink>
        <div
          v-else-if="quizUnlocked === false"
          class="flex items-center gap-3 bg-surface-dim/70 px-5 py-4 text-neutral-medium"
          aria-disabled="true"
        >
          <LockKeyhole class="size-5 shrink-0" />
          <span class="min-w-0 flex-1 text-sm font-bold"
            >{{ lessons.length + 1 }}. Quiz Akhir Course</span
          >
        </div>
        <div
          v-else
          class="flex items-center gap-3 bg-surface-dim/70 px-5 py-4 text-neutral-medium"
          aria-disabled="true"
        >
          <LockKeyhole class="size-5 shrink-0" />
          <span class="min-w-0 flex-1 text-sm font-bold"
            >{{ lessons.length + 1 }}. Status Quiz Tidak Tersedia</span
          >
        </div>
      </li>
    </ol>
  </aside>
</template>
