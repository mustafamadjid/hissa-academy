<script setup lang="ts">
import { computed, ref, toRef } from "vue";

import { useYouTubePlayer } from "../composables/useYouTubePlayer";

const props = defineProps<{
  lessonId: string;
  videoId: string;
  title: string;
  startSeconds: number;
}>();

const emit = defineEmits<{
  watchSample: [lessonId: string, positionSeconds: number, watchedSeconds: number];
  playbackStopped: [lessonId: string, positionSeconds: number];
}>();

const playerContainer = ref<HTMLElement | null>(null);
const playerError = ref<string | null>(null);
const accessibleLabel = computed(() => `Video: ${props.title}`);

useYouTubePlayer({
  container: playerContainer,
  videoId: toRef(props, "videoId"),
  startSeconds: toRef(props, "startSeconds"),
  onWatchSample: (position, watched) =>
    emit("watchSample", props.lessonId, position, watched),
  onPlaybackStopped: (position) =>
    emit("playbackStopped", props.lessonId, position),
  onError: (message) => {
    playerError.value = message;
  },
});
</script>

<template>
  <div class="size-full">
    <div ref="playerContainer" class="size-full" :aria-label="accessibleLabel" />
    <div v-if="playerError" role="alert" class="grid size-full place-items-center px-6 text-center text-white">
      {{ playerError }}
    </div>
  </div>
</template>
