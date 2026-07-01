<script setup lang="ts">
import { ref, watch } from "vue";
import {
  ArrowDown,
  ArrowUp,
  BookOpen,
  ChevronDown,
  Clock3,
  FileText,
  GripVertical,
  Pencil,
  Trash2,
  Video,
} from "@lucide/vue";
import Draggable from "vuedraggable";

import type { AdminLessonDto } from "../types/course.types";
import { generateYoutubeEmbedUrl } from "../utils/youtube-video";

const props = defineProps<{
  lessons: readonly AdminLessonDto[];
  disabled?: boolean;
}>();

const emit = defineEmits<{
  edit: [lesson: AdminLessonDto];
  delete: [lesson: AdminLessonDto];
  reorder: [lessons: AdminLessonDto[]];
}>();

const localLessons = ref<AdminLessonDto[]>([]);
const expandedLessonIds = ref<Set<string>>(new Set());

interface DraggableEndEvent {
  oldIndex?: number;
  newIndex?: number;
}

function syncLessons(): void {
  localLessons.value = props.lessons.map((lesson) => ({ ...lesson }));

  const availableLessonIds = new Set(props.lessons.map((lesson) => lesson.id));
  expandedLessonIds.value = new Set(
    [...expandedLessonIds.value].filter((lessonId) =>
      availableLessonIds.has(lessonId),
    ),
  );
}

function isLessonExpanded(lessonId: string): boolean {
  return expandedLessonIds.value.has(lessonId);
}

function toggleLesson(lessonId: string): void {
  const nextExpandedLessonIds = new Set(expandedLessonIds.value);

  if (nextExpandedLessonIds.has(lessonId)) {
    nextExpandedLessonIds.delete(lessonId);
  } else {
    nextExpandedLessonIds.add(lessonId);
  }

  expandedLessonIds.value = nextExpandedLessonIds;
}

function getLessonEmbedUrl(lesson: AdminLessonDto): string | null {
  const videoId = lesson.video?.youtube_video_id;

  return videoId ? generateYoutubeEmbedUrl(videoId) : null;
}

function emitReorder(): void {
  emit(
    "reorder",
    localLessons.value.map((lesson) => ({ ...lesson })),
  );
}

function handleDragEnd(event: DraggableEndEvent): void {
  if (event.oldIndex === event.newIndex) return;

  emitReorder();
}

function moveLesson(index: number, offset: -1 | 1): void {
  const targetIndex = index + offset;
  if (
    props.disabled ||
    targetIndex < 0 ||
    targetIndex >= localLessons.value.length
  ) {
    return;
  }

  const [lesson] = localLessons.value.splice(index, 1);
  if (!lesson) return;

  localLessons.value.splice(targetIndex, 0, lesson);
  emitReorder();
}

function formatDuration(
  totalSeconds: number | null | undefined,
): string | null {
  if (totalSeconds === null || totalSeconds === undefined) return null;

  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;

  if (hours > 0) return `${hours}j ${minutes}m`;
  if (minutes > 0) return `${minutes}m ${seconds}d`;
  return `${seconds} detik`;
}

watch(() => props.lessons, syncLessons, { immediate: true, deep: true });
</script>

<template>
  <section aria-labelledby="lesson-list-title">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
      <div>
        <h2 id="lesson-list-title" class="text-xl font-bold text-emerald-950">
          Daftar Lesson
        </h2>
        <p class="mt-1 text-xs leading-5 text-slate-500">
          Tarik ikon pegangan untuk mengubah urutan, lalu perubahan disimpan
          otomatis.
        </p>
      </div>
      <span class="text-xs font-semibold text-slate-500">
        {{ lessons.length }} lesson
      </span>
    </div>

    <div
      v-if="disabled && lessons.length > 0"
      role="status"
      class="mb-3 text-xs font-medium text-emerald-700"
    >
      Menyimpan urutan lesson...
    </div>

    <Draggable
      v-if="localLessons.length > 0"
      v-model="localLessons"
      item-key="id"
      tag="ol"
      handle=".lesson-drag-handle"
      :disabled="disabled"
      :animation="180"
      ghost-class="opacity-40"
      class="space-y-3"
      @end="handleDragEnd"
    >
      <template #item="{ element: lesson, index }">
        <div class="cursor-pointer">
          <li
            class="cursor-pointer grid grid-cols-[auto_minmax(0,1fr)] items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-4 shadow-sm transition-shadow hover:shadow-md sm:grid-cols-[auto_minmax(0,1fr)_auto] sm:px-4"
          >
            <button
              type="button"
              class=" lesson-drag-handle flex size-9 cursor-grab items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 active:cursor-grabbing disabled:cursor-not-allowed"
              :disabled="disabled"
              :aria-label="`Tarik untuk mengurutkan ${lesson.title}`"
            >
              <GripVertical :size="18" aria-hidden="true" />
            </button>

            <button
              type="button"
              class="cursor-pointer grid min-w-0 grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3 rounded-lg text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
              :aria-expanded="isLessonExpanded(lesson.id)"
              :aria-controls="`lesson-video-${lesson.id}`"
              @click="toggleLesson(lesson.id)"
            >
              <span
                class="cursor-pointer flex size-10 items-center justify-center rounded-lg"
                :class="
                  lesson.video
                    ? 'bg-emerald-50 text-emerald-600'
                    : 'bg-slate-100 text-slate-500'
                "
              >
                <Video v-if="lesson.video" :size="18" aria-hidden="true" />
                <FileText v-else :size="18" aria-hidden="true" />
              </span>

              <span class="min-w-0">
                <span
                  class="cursor-pointer block truncate text-sm font-semibold text-slate-800"
                >
                  <span class="cursor-pointer mr-2 text-slate-400">{{ index + 1 }}.</span>
                  {{ lesson.title }}
                </span>
                <span
                  class="cursor-pointer mt-1 flex flex-wrap items-center gap-2 text-[11px] text-slate-500"
                >
                  <span>{{
                    lesson.video ? "Video YouTube" : "Tanpa video"
                  }}</span>
                  <template
                    v-if="formatDuration(lesson.video?.duration_seconds)"
                  >
                    <span aria-hidden="true">&bull;</span>
                    <span class="cursor-pointer inline-flex items-center gap-1">
                      <Clock3 :size="12" aria-hidden="true" />
                      {{ formatDuration(lesson.video?.duration_seconds) }}
                    </span>
                  </template>
                  <span
                    v-if="lesson.is_required"
                    class="cursor-pointer rounded-full bg-amber-50 px-2 py-0.5 font-semibold text-amber-700"
                  >
                    Wajib
                  </span>
                </span>
              </span>

              <ChevronDown
                :size="18"
                aria-hidden="true"
                class="cursor-pointer text-slate-400 transition-transform duration-200"
                :class="isLessonExpanded(lesson.id) ? 'rotate-180' : ''"
              />
            </button>

            <div class="cursor-pointer col-span-2 flex justify-end gap-1 sm:col-span-1">
              <UButton
                color="neutral"
                variant="ghost"
                size="sm"
                :disabled="disabled || index === 0"
                :aria-label="`Naikkan urutan ${lesson.title}`"
                @click="moveLesson(index, -1)"
              >
                <ArrowUp :size="15" aria-hidden="true" />
              </UButton>
              <UButton
                color="neutral"
                variant="ghost"
                size="sm"
                :disabled="disabled || index === localLessons.length - 1"
                :aria-label="`Turunkan urutan ${lesson.title}`"
                @click="moveLesson(index, 1)"
              >
                <ArrowDown :size="15" aria-hidden="true" />
              </UButton>
              <UButton
                color="neutral"
                variant="ghost"
                size="sm"
                :disabled="disabled"
                :aria-label="`Edit ${lesson.title}`"
                @click="emit('edit', lesson)"
              >
                <Pencil :size="15" aria-hidden="true" />
                Edit
              </UButton>
              <UButton
                color="error"
                variant="ghost"
                size="sm"
                :disabled="disabled"
                :aria-label="`Hapus ${lesson.title}`"
                @click="emit('delete', lesson)"
              >
                <Trash2 :size="15" aria-hidden="true" />
                Hapus
              </UButton>
            </div>

            <div
              v-if="isLessonExpanded(lesson.id)"
              :id="`lesson-video-${lesson.id}`"
              class="cursor-pointer col-span-2 border-t border-slate-100 pt-4 sm:col-span-3"
            >
              <div
                v-if="getLessonEmbedUrl(lesson)"
                class="cursor-pointer aspect-video w-full overflow-hidden rounded-lg bg-slate-950"
              >
                <iframe
                  class="cursor-pointer size-full border-0"
                  :src="getLessonEmbedUrl(lesson) ?? undefined"
                  :title="`Video lesson ${lesson.title}`"
                  loading="lazy"
                  allow="
                    accelerometer;
                    autoplay;
                    clipboard-write;
                    encrypted-media;
                    gyroscope;
                    picture-in-picture;
                    web-share;
                  "
                  referrerpolicy="strict-origin-when-cross-origin"
                  allowfullscreen
                />
              </div>
              <p
                v-else
                role="status"
                class="cursor-pointer rounded-lg bg-slate-50 px-4 py-8 text-center text-sm text-slate-500"
              >
                Video YouTube belum tersedia untuk lesson ini.
              </p>
            </div>
          </li>
        </div>
      </template>
    </Draggable>

    <div
      v-else
      class="flex flex-col items-center rounded-xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center"
    >
      <span
        class="mb-3 flex size-11 items-center justify-center rounded-full bg-slate-100 text-slate-400"
      >
        <BookOpen :size="21" aria-hidden="true" />
      </span>
      <p class="text-sm font-semibold text-slate-800">Belum ada lesson</p>
      <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">
        Course ini belum memiliki materi pembelajaran.
      </p>
    </div>
  </section>
</template>
