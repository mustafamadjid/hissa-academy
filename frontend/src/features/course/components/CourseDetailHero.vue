<script setup lang="ts">
import { computed } from "vue";
import type { DeepReadonly } from "vue";
import {
  BookOpen,
  CheckCircle2,
  ListVideo,
  Pencil,
  CircleHelp,
  Target,
  Video,
} from "@lucide/vue";

import type { AdminCourseDetailDto } from "../types/course.types";

const props = defineProps<{
  course: DeepReadonly<AdminCourseDetailDto>;
}>();

const emit = defineEmits<{
  edit: [];
  manageLessons: [];
  manageQuiz: [];
}>();

const statusLabel = computed(() => {
  const labels: Record<string, string> = {
    active: "Aktif",
    draft: "Draft",
    inactive: "Nonaktif",
    published: "Published",
  };

  return labels[props.course.status] ?? props.course.status;
});
</script>

<template>
  <section
    class="relative overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
    aria-labelledby="course-title"
  >
    <div
      class="pointer-events-none absolute -right-16 -top-28 size-80 rounded-full bg-emerald-50/80"
      aria-hidden="true"
    />

    <div
      class="relative grid gap-6 p-5 sm:p-6 lg:grid-cols-[200px_minmax(0,1fr)_auto] lg:items-center lg:p-8"
    >
      <div
        class="flex h-36 overflow-hidden rounded-xl border border-emerald-100 bg-emerald-950 shadow-sm lg:h-32"
      >
        <div
          class="relative flex h-full w-full items-center justify-center overflow-hidden bg-linear-to-br from-emerald-950 via-emerald-800 to-emerald-600"
          aria-hidden="true"
        >
          <div
            class="absolute -left-7 -top-8 size-24 rounded-full bg-white/10"
          />
          <div
            class="absolute -bottom-12 -right-6 size-32 rounded-full bg-lime-300/15"
          />
          <BookOpen :size="54" class="relative text-white" stroke-width="1.4" />
        </div>
      </div>

      <div class="min-w-0">
        <div class="mb-2 flex flex-wrap items-center gap-2">
          <span
            class="rounded bg-emerald-800 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white"
          >
            Course
          </span>

          <span
            class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700"
          >
            <CheckCircle2 :size="12" aria-hidden="true" />
            {{ statusLabel }}
          </span>
        </div>

        <h1
          id="course-title"
          class="text-2xl font-bold leading-tight tracking-tight text-emerald-950 sm:text-3xl"
        >
          {{ course.name }}
        </h1>

        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
          {{ course.description }}
        </p>

        <div
          class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs font-semibold text-slate-700"
        >
          <span class="inline-flex items-center gap-2">
            <Video :size="16" class="text-emerald-600" aria-hidden="true" />
            {{ course.total_lessons }} lesson
          </span>
          <span class="inline-flex items-center gap-2">
            <Target :size="16" class="text-emerald-600" aria-hidden="true" />
            Nilai minimum {{ course.minimum_score }}
          </span>
        </div>
      </div>

      <div
        class="flex w-full flex-col gap-4 self-start lg:w-auto lg:self-center"
      >
        <UButton
          color="neutral"
          variant="outline"
          size="md"
          class="w-full p-3 cursor-pointer justify-center border-slate-200 bg-white text-slate-700 shadow-none hover:bg-slate-50 lg:w-auto"
          @click="emit('edit')"
        >
          <Pencil :size="16" aria-hidden="true" />
          Edit Course
        </UButton>

        <UButton
          color="primary"
          variant="solid"
          size="md"
          class="w-full p-3 cursor-pointer justify-center bg-emerald-700 text-white shadow-none hover:bg-emerald-800 lg:w-auto"
          @click="emit('manageLessons')"
        >
          <ListVideo :size="16" aria-hidden="true" />
          Kelola Lesson
        </UButton>

        <UButton
          color="primary"
          variant="solid"
          size="md"
          class="w-full p-3 cursor-pointer justify-center bg-emerald-700 text-white shadow-none hover:bg-emerald-800 lg:w-auto"
          @click="emit('manageQuiz')"
        >
          <CircleHelp :size="16" aria-hidden="true" />
          Kelola Quiz
        </UButton>
      </div>
    </div>
  </section>
</template>
