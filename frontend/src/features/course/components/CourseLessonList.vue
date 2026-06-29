<script setup lang="ts">
import { BookOpen, Clock3, FileText, GripVertical, Video } from '@lucide/vue'

import type { AdminLessonDto } from '../types/course.types'

defineProps<{
  lessons: readonly AdminLessonDto[]
}>()

function formatDuration(totalSeconds: number | null | undefined): string | null {
  if (totalSeconds === null || totalSeconds === undefined) return null

  const hours = Math.floor(totalSeconds / 3600)
  const minutes = Math.floor((totalSeconds % 3600) / 60)
  const seconds = totalSeconds % 60

  if (hours > 0) return `${hours}j ${minutes}m`
  if (minutes > 0) return `${minutes}m ${seconds}d`
  return `${seconds} detik`
}
</script>

<template>
  <section aria-labelledby="curriculum-title">
    <div class="mb-4">
      <h2 id="curriculum-title" class="text-xl font-bold text-emerald-950">
        Kurikulum &amp; Materi
      </h2>
      <p class="mt-1 text-xs leading-5 text-slate-500">
        Materi ditampilkan sesuai urutan pembelajaran course.
      </p>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div
        class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-50/70 px-4 py-3.5 sm:px-5"
      >
        <div>
          <h3 class="text-xs font-bold text-emerald-950">Daftar Lesson</h3>
          <p class="mt-1 text-[11px] text-slate-500">
            {{ lessons.length }} materi pembelajaran
          </p>
        </div>

        <BookOpen :size="18" class="text-slate-400" aria-hidden="true" />
      </div>

      <ol v-if="lessons.length > 0" class="divide-y divide-slate-200">
        <li
          v-for="lesson in lessons"
          :key="lesson.id"
          class="grid grid-cols-[auto_auto_minmax(0,1fr)] items-center gap-3 px-4 py-4 sm:grid-cols-[auto_auto_minmax(0,1fr)_auto] sm:px-5"
        >
          <GripVertical
            :size="16"
            class="text-slate-400"
            aria-hidden="true"
          />

          <span
            class="flex size-9 items-center justify-center rounded-lg"
            :class="
              lesson.video
                ? 'bg-emerald-50 text-emerald-600'
                : 'bg-slate-100 text-slate-500'
            "
          >
            <Video v-if="lesson.video" :size="17" aria-hidden="true" />
            <FileText v-else :size="17" aria-hidden="true" />
          </span>

          <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-slate-800">
              <span class="mr-2 text-slate-400">{{ lesson.position }}.</span>
              {{ lesson.title }}
            </p>

            <div class="mt-1 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
              <span>{{ lesson.video ? 'Video lesson' : 'Materi lesson' }}</span>
              <template v-if="formatDuration(lesson.video?.duration_seconds)">
                <span aria-hidden="true">&bull;</span>
                <span class="inline-flex items-center gap-1">
                  <Clock3 :size="12" aria-hidden="true" />
                  {{ formatDuration(lesson.video?.duration_seconds) }}
                </span>
              </template>
            </div>
          </div>

          <span
            v-if="lesson.is_required"
            class="col-start-3 w-fit rounded-full border border-amber-200 bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700 sm:col-start-auto"
          >
            Wajib
          </span>
        </li>
      </ol>

      <div v-else class="flex flex-col items-center px-5 py-12 text-center">
        <span
          class="mb-3 flex size-11 items-center justify-center rounded-full bg-slate-100 text-slate-400"
        >
          <BookOpen :size="21" aria-hidden="true" />
        </span>
        <p class="text-sm font-semibold text-slate-800">Belum ada lesson</p>
        <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">
          Materi pembelajaran untuk course ini belum tersedia.
        </p>
      </div>
    </div>
  </section>
</template>
