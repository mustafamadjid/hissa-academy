<script setup lang="ts">
import { ArrowLeft, BookOpen, LoaderCircle, LockKeyhole, PlayCircle } from "@lucide/vue";
import { computed, watch } from "vue";
import { useRoute } from "vue-router";

import GuestLayout from "@/layouts/Guest/GuestLayout.vue";

import LessonPlaylist from "../components/LessonPlaylist.vue";
import YouTubeLessonPlayer from "../components/YouTubeLessonPlayer.vue";
import { useLessonDetail } from "../composables/useLessonDetail";
import { useLessonProgress } from "../composables/useLessonProgress";

const route = useRoute();
const lessonId = computed(() => String(route.params.lessonId));
const { lesson, course, isLoading, error, isLocked, fetchLesson } = useLessonDetail();
const { progressError, setLesson, recordWatch, flush } = useLessonProgress();

const orderedLessons = computed(() =>
  [...(course.value?.lessons ?? [])].sort((left, right) => left.position - right.position),
);
const quizUnlocked = computed(() =>
  orderedLessons.value
    .filter((item) => item.is_required)
    .every((item) => item.progress?.status === "completed"),
);

watch(lessonId, (id) => void fetchLesson(id), { immediate: true });
watch(lesson, (currentLesson) => {
  if (currentLesson) setLesson(currentLesson.id, currentLesson.progress);
});

</script>

<template>
  <GuestLayout>
    <main class="min-h-screen bg-background text-neutral-high">
      <div v-if="isLoading" class="grid min-h-[70vh] place-items-center">
        <div class="text-center">
          <LoaderCircle class="mx-auto size-10 animate-spin text-primary-green" />
          <p class="mt-4 text-sm text-neutral-medium">Memuat lesson...</p>
        </div>
      </div>

      <section v-else-if="error || !lesson || !course" class="mx-auto grid min-h-[70vh] max-w-xl place-items-center px-5 text-center">
        <div>
          <div class="mx-auto grid size-16 place-items-center rounded-2xl bg-primary-green/10">
            <LockKeyhole v-if="isLocked" class="size-8 text-primary-green" />
            <BookOpen v-else class="size-8 text-primary-green" />
          </div>
          <h1 class="mt-5 text-2xl font-bold">{{ isLocked ? "Lesson masih terkunci" : "Lesson tidak tersedia" }}</h1>
          <p class="mt-3 leading-7 text-neutral-medium">{{ error }}</p>
          <RouterLink :to="{ name: 'guest-courses' }" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-primary-dark-green px-5 py-3 text-sm font-bold text-white">
            <ArrowLeft class="size-4" /> Kembali ke course
          </RouterLink>
        </div>
      </section>

      <template v-else>
        <section class="bg-[#eaf8f4] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
          <div class="mx-auto max-w-7xl overflow-hidden rounded-2xl border border-primary-green/15 bg-white shadow-elevation-2 lg:grid lg:min-h-[560px] lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="flex min-w-0 flex-col bg-[#07130e]">
              <div class="aspect-video w-full lg:my-auto">
                <YouTubeLessonPlayer
                  v-if="lesson.video?.youtube_video_id"
                  :lesson-id="lesson.id"
                  :video-id="lesson.video.youtube_video_id"
                  :key="lesson.id"
                  :title="lesson.video.title ?? lesson.title"
                  :start-seconds="lesson.progress?.last_position_seconds ?? 0"
                  @watch-sample="recordWatch"
                  @playback-stopped="flush"
                />
                <div v-else class="grid size-full place-items-center text-center text-white">
                  <div><PlayCircle class="mx-auto size-16 text-lime-accent" /><p class="mt-4 font-semibold">Video lesson belum tersedia</p></div>
                </div>
              </div>
            </div>
            <LessonPlaylist
              :lessons="orderedLessons"
              :active-lesson-id="lesson.id"
              :course-id="course.id"
              :quiz-unlocked="quizUnlocked"
            />
          </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:py-14">
          <p v-if="progressError" role="status" class="mb-5 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ progressError }} Progress akan dicoba kembali saat video dilanjutkan.
          </p>
          <RouterLink :to="{ name: 'course-detail', params: { courseId: lesson.course_id } }" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-green">
            <ArrowLeft class="size-4" /> {{ course.name }}
          </RouterLink>
          <p class="mt-7 text-xs font-bold uppercase tracking-[0.16em] text-primary-green">Lesson {{ lesson.position }}</p>
          <h1 class="mt-2 text-3xl font-bold tracking-tight text-primary-dark-green sm:text-4xl">{{ lesson.title }}</h1>
          <p v-if="lesson.video?.description" class="mt-5 max-w-4xl whitespace-pre-line text-sm leading-7 text-neutral-medium">{{ lesson.video.description }}</p>
        </section>
      </template>
    </main>
  </GuestLayout>
</template>
