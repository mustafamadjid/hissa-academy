<script setup lang="ts">
import { ArrowRight, BookOpen, CircleCheck } from "@lucide/vue";

import type { StudentCourseSummaryDto } from "../types/course-catalog.types";

defineProps<{ course: StudentCourseSummaryDto }>();
</script>

<template>
  <RouterLink
    :to="{ name: 'course-detail', params: { courseId: course.id } }"
    class="block h-full rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary-green"
    :aria-label="`Lihat detail course ${course.name}`"
  >
    <article
      class="group flex h-full flex-col overflow-hidden rounded-2xl border border-neutral-low/70 bg-white shadow-[0_4px_20px_rgba(16,24,40,0.05)] transition duration-300 hover:-translate-y-1 hover:shadow-elevation-3"
    >
      <div class="p-3">
        <div
          class="relative flex h-48 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-primary-dark-green via-primary-green to-emerald-accent"
        >
          <div
            class="absolute -right-10 -top-10 size-36 rounded-full bg-lime-accent/20"
          />
          <div
            class="absolute -bottom-12 -left-8 size-36 rounded-full bg-white/10"
          />
          <BookOpen
            class="relative size-16 text-white/90"
            :stroke-width="1.3"
          />
          <span
            class="absolute bottom-4 left-4 rounded-lg bg-white/95 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-primary-green shadow-sm"
          >
            Nilai minimum {{ course.minimum_score }}
          </span>
        </div>
      </div>

      <div class="flex flex-1 flex-col px-6 pb-5 pt-2">
        <h2
          class="mb-2 line-clamp-2 text-lg font-bold leading-snug text-neutral-high transition-colors group-hover:text-primary-green"
        >
          {{ course.name }}
        </h2>
        <p
          class="mb-5 line-clamp-3 flex-1 text-sm leading-6 text-neutral-medium"
        >
          {{ course.description }}
        </p>

        <div v-if="course.progress_percentage > 0" class="mb-4">
          <div
            class="mb-2 flex items-center justify-between text-xs font-semibold"
          >
            <span class="text-neutral-medium">Progress belajar</span>
            <span class="text-primary-green"
              >{{ course.progress_percentage }}%</span
            >
          </div>
          <div class="h-2 overflow-hidden rounded-full bg-surface-dim">
            <div
              class="h-full rounded-full bg-primary-green transition-all"
              :style="{ width: `${course.progress_percentage}%` }"
            />
          </div>
        </div>

        <div
          class="flex items-center justify-between gap-3 border-t border-neutral-low/70 pt-4"
        >
          <span
            class="inline-flex items-center gap-1.5 text-xs text-neutral-medium"
          >
            <CircleCheck class="size-4 text-primary-green" />
            {{ course.completed_lessons }}/{{ course.total_lessons }} materi
          </span>
          <span
            class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-green"
          >
            Pelajari sekarang
            <ArrowRight
              class="size-4 transition-transform group-hover:translate-x-1"
            />
          </span>
        </div>
      </div>
    </article>
  </RouterLink>
</template>
