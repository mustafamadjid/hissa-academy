<script setup lang="ts">
import { ref } from 'vue'
import {
  BookOpen,
  ChevronDown,
  Clock3,
  FileText,
  PlayCircle,
  Video,
} from '@lucide/vue'

import type { AdminLessonDto } from '../types/course.types'
import {
  extractYoutubeVideoId,
  generateYoutubeEmbedUrl,
} from '../utils/youtube-video'

defineProps<{
  lessons: readonly AdminLessonDto[]
}>()

const expandedLessonId = ref<string | null>(null)

function getLessonEmbedUrl(lesson: AdminLessonDto): string | null {
  const videoId =
    lesson.video?.youtube_video_id ??
    (lesson.video?.video_url
      ? extractYoutubeVideoId(lesson.video.video_url)
      : null)

  return videoId ? generateYoutubeEmbedUrl(videoId) : null
}

function toggleLesson(lesson: AdminLessonDto): void {
  if (!getLessonEmbedUrl(lesson)) return

  expandedLessonId.value =
    expandedLessonId.value === lesson.id ? null : lesson.id
}

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
          class="px-4 py-4 sm:px-5"
        >
          <button
            type="button"
            class="grid w-full grid-cols-[6rem_minmax(0,1fr)_auto] items-center gap-3 rounded-lg text-left transition-colors focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-emerald-600 sm:grid-cols-[8rem_minmax(0,1fr)_auto]"
            :class="
              getLessonEmbedUrl(lesson)
                ? 'cursor-pointer hover:bg-slate-50'
                : 'cursor-default'
            "
            :aria-expanded="
              getLessonEmbedUrl(lesson)
                ? expandedLessonId === lesson.id
                : undefined
            "
            :aria-controls="
              getLessonEmbedUrl(lesson) ? `lesson-video-${lesson.id}` : undefined
            "
            @click="toggleLesson(lesson)"
          >
            <span
              class="relative aspect-video overflow-hidden rounded-lg bg-slate-100"
            >
              <img
                v-if="lesson.video?.thumbnail_url"
                :src="lesson.video.thumbnail_url"
                :alt="`Thumbnail ${lesson.title}`"
                class="size-full object-cover"
                loading="lazy"
              />
              <span
                v-else
                class="flex size-full items-center justify-center text-slate-400"
                aria-hidden="true"
              >
                <FileText v-if="!lesson.video" :size="22" />
                <Video v-else :size="22" />
              </span>
              <span
                v-if="getLessonEmbedUrl(lesson)"
                class="absolute inset-0 flex items-center justify-center bg-slate-950/20 text-white"
                aria-hidden="true"
              >
                <PlayCircle :size="30" fill="currentColor" class="drop-shadow" />
              </span>
            </span>

            <span class="min-w-0">
              <span class="block truncate text-sm font-semibold text-slate-800">
                <span class="mr-2 text-slate-400">{{ lesson.position }}.</span>
                {{ lesson.title }}
              </span>

              <span
                class="mt-1 flex flex-wrap items-center gap-2 text-[11px] text-slate-500"
              >
                <span>{{ lesson.video ? 'Video lesson' : 'Materi lesson' }}</span>
                <template v-if="formatDuration(lesson.video?.duration_seconds)">
                  <span aria-hidden="true">&bull;</span>
                  <span class="inline-flex items-center gap-1">
                    <Clock3 :size="12" aria-hidden="true" />
                    {{ formatDuration(lesson.video?.duration_seconds) }}
                  </span>
                </template>
              </span>
            </span>

            <span class="flex items-center justify-end gap-3">
              <span
                v-if="lesson.is_required"
                class="hidden w-fit rounded-full border border-amber-200 bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700 sm:inline-flex"
              >
                Wajib
              </span>

              <ChevronDown
                v-if="getLessonEmbedUrl(lesson)"
                :size="18"
                class="text-slate-400 transition-transform duration-200"
                :class="expandedLessonId === lesson.id ? 'rotate-180' : ''"
                aria-hidden="true"
              />
              <span v-else class="w-4" aria-hidden="true" />
            </span>
          </button>

          <div
            v-if="expandedLessonId === lesson.id && getLessonEmbedUrl(lesson)"
            :id="`lesson-video-${lesson.id}`"
            class="mt-4 aspect-video w-full overflow-hidden rounded-lg bg-slate-950"
          >
            <iframe
              class="size-full border-0"
              :src="getLessonEmbedUrl(lesson) ?? undefined"
              :title="`Video lesson ${lesson.title}`"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            />
          </div>
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
