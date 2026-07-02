import { onBeforeUnmount, readonly, ref } from "vue";

import { submitLessonProgress } from "../api/lesson.api";
import type { LessonProgressDto } from "../types/lesson.types";

const HEARTBEAT_SECONDS = 15;

export function useLessonProgress() {
  const progress = ref<LessonProgressDto | null>(null);
  const error = ref<string | null>(null);
  let activeLessonId: string | null = null;
  const lastPositions = new Map<string, number>();
  const pendingSeconds = new Map<string, number>();
  const requests = new Map<string, Promise<void>>();

  function setLesson(lessonId: string, initialProgress: LessonProgressDto | null): void {
    activeLessonId = lessonId;
    progress.value = initialProgress;
    lastPositions.set(lessonId, initialProgress?.last_position_seconds ?? 0);
    error.value = null;
  }

  async function sendPending(lessonId: string): Promise<void> {
    const pendingWatchedSeconds = pendingSeconds.get(lessonId) ?? 0;
    if (pendingWatchedSeconds < 1) return;
    const existingRequest = requests.get(lessonId);
    if (existingRequest) return existingRequest;

    const watchedSeconds = pendingWatchedSeconds;
    const positionSeconds = lastPositions.get(lessonId) ?? 0;
    pendingSeconds.set(lessonId, pendingWatchedSeconds - watchedSeconds);

    const request = submitLessonProgress(lessonId, {
      last_position_seconds: positionSeconds,
      watched_seconds: watchedSeconds,
    })
      .then((response) => {
        if (activeLessonId === lessonId) progress.value = response.data;
        error.value = null;
      })
      .catch((caughtError: unknown) => {
        pendingSeconds.set(
          lessonId,
          (pendingSeconds.get(lessonId) ?? 0) + watchedSeconds,
        );
        error.value =
          caughtError instanceof Error
            ? caughtError.message
            : "Progress video gagal disimpan.";
      })
      .finally(() => {
        requests.delete(lessonId);
        if ((pendingSeconds.get(lessonId) ?? 0) >= HEARTBEAT_SECONDS) {
          void sendPending(lessonId);
        }
      });

    requests.set(lessonId, request);
    return request;
  }

  function recordWatch(
    lessonId: string,
    positionSeconds: number,
    watchedSeconds: number,
  ): void {
    lastPositions.set(lessonId, positionSeconds);
    const pending = (pendingSeconds.get(lessonId) ?? 0) + watchedSeconds;
    pendingSeconds.set(lessonId, pending);
    if (pending >= HEARTBEAT_SECONDS) void sendPending(lessonId);
  }

  function flush(lessonId: string, positionSeconds: number): void {
    lastPositions.set(lessonId, positionSeconds);
    void sendPending(lessonId);
  }

  onBeforeUnmount(() => {
    for (const lessonId of pendingSeconds.keys()) void sendPending(lessonId);
  });

  return {
    progress: readonly(progress),
    progressError: readonly(error),
    setLesson,
    recordWatch,
    flush,
  };
}
