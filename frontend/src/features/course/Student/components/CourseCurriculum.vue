<script setup lang="ts">
import { CheckCircle2, Circle, LockKeyhole, PlayCircle, X } from "@lucide/vue";
import { computed, onBeforeUnmount, ref, watch } from "vue";

import {
  extractYoutubeVideoId,
  generateYoutubeEmbedUrl,
} from "@/features/course/utils/youtube-video";
import type { StudentCourseLessonDto } from "../types/course-catalog.types";

defineProps<{
  lessons: readonly StudentCourseLessonDto[];
  authenticated: boolean;
}>();

const selectedLesson = ref<StudentCourseLessonDto | null>(null);

const embedUrl = computed(() => {
  const video = selectedLesson.value?.video;
  if (!video) return null;

  const videoId =
    video.youtube_video_id ?? extractYoutubeVideoId(video.video_url);
  return videoId ? `${generateYoutubeEmbedUrl(videoId)}?autoplay=1` : null;
});

function openVideo(lesson: StudentCourseLessonDto): void {
  if (!lesson.video) return;
  selectedLesson.value = lesson;
}

function closeVideo(): void {
  selectedLesson.value = null;
}

function handleKeydown(event: KeyboardEvent): void {
  if (event.key === "Escape" && selectedLesson.value) closeVideo();
}

watch(selectedLesson, (lesson) => {
  document.body.style.overflow = lesson ? "hidden" : "";
});

document.addEventListener("keydown", handleKeydown);

onBeforeUnmount(() => {
  document.removeEventListener("keydown", handleKeydown);
  document.body.style.overflow = "";
});
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
        <button
          v-if="!lesson.is_locked"
          type="button"
          class="flex w-full items-center gap-4 px-5 py-4 text-left transition hover:bg-primary-green/5 focus-visible:outline-2 focus-visible:outline-primary-green disabled:cursor-not-allowed disabled:opacity-60"
          :disabled="!lesson.video"
          @click="openVideo(lesson)"
        >
          <CheckCircle2
            v-if="lesson.progress?.status === 'completed'"
            class="size-5 shrink-0 text-primary-green"
          />
          <PlayCircle v-else class="size-5 shrink-0 text-primary-green" />
          <span class="min-w-0 flex-1 text-sm font-medium text-neutral-high"
            >{{ lesson.position }}. {{ lesson.title }}</span
          >
          <span class="text-xs font-semibold text-primary-green">{{
            lesson.video ? "Putar" : "Video belum tersedia"
          }}</span>
        </button>
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
      <li
        v-if="lessons.length === 0"
        class="flex items-center gap-3 px-5 py-6 text-sm text-neutral-medium"
      >
        <Circle class="size-5" /> Belum ada lesson pada course ini.
      </li>
    </ol>

    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        leave-active-class="transition duration-150 ease-in"
        leave-to-class="opacity-0"
      >
        <div
          v-if="selectedLesson"
          class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4 backdrop-blur-sm sm:p-8"
          role="dialog"
          aria-modal="true"
          :aria-labelledby="`lesson-video-title-${selectedLesson.id}`"
          @click.self="closeVideo"
        >
          <div
            class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl"
          >
            <button
              type="button"
              class="absolute right-3 top-3 z-10 grid size-10 place-items-center rounded-full bg-black/65 text-white transition hover:bg-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
              aria-label="Tutup video"
              @click="closeVideo"
            >
              <X class="size-5" />
            </button>

            <div class="aspect-video w-full bg-black">
              <iframe
                v-if="embedUrl"
                :src="embedUrl"
                :title="`Video lesson ${selectedLesson.title}`"
                class="size-full border-0"
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
              <video
                v-else-if="selectedLesson.video?.video_url"
                :src="selectedLesson.video.video_url"
                class="size-full"
                controls
                autoplay
              />
            </div>

            <p
              :id="`lesson-video-title-${selectedLesson.id}`"
              class="px-5 py-4 text-center text-base font-semibold text-neutral-high sm:text-lg"
            >
              {{ selectedLesson.title }}
            </p>
          </div>
        </div>
      </Transition>
    </Teleport>
  </section>
</template>
