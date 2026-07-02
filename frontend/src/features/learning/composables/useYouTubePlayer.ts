import { onBeforeUnmount, onMounted, type Ref, watch } from "vue";

import {
  YOUTUBE_PLAYER_STATE,
  type YouTubePlayer,
  type YouTubePlayerEvent,
} from "../types/youtube-player.types";

const YOUTUBE_API_URL = "https://www.youtube.com/iframe_api";
let youtubeApiPromise: Promise<void> | null = null;

function loadYouTubeApi(): Promise<void> {
  if (window.YT?.Player) return Promise.resolve();
  if (youtubeApiPromise) return youtubeApiPromise;

  youtubeApiPromise = new Promise<void>((resolve, reject) => {
    const previousCallback = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = () => {
      previousCallback?.();
      resolve();
    };

    const existingScript = document.querySelector<HTMLScriptElement>(
      `script[src="${YOUTUBE_API_URL}"]`,
    );
    if (existingScript) return;

    const script = document.createElement("script");
    script.src = YOUTUBE_API_URL;
    script.async = true;
    script.onerror = () => {
      youtubeApiPromise = null;
      reject(new Error("YouTube Player API gagal dimuat."));
    };
    document.head.appendChild(script);
  });

  return youtubeApiPromise;
}

interface UseYouTubePlayerOptions {
  container: Ref<HTMLElement | null>;
  videoId: Ref<string>;
  startSeconds: Ref<number>;
  onWatchSample(positionSeconds: number, watchedSeconds: number): void;
  onPlaybackStopped(positionSeconds: number): void;
  onError(message: string): void;
}

export function useYouTubePlayer(options: UseYouTubePlayerOptions) {
  let player: YouTubePlayer | null = null;
  let sampleTimer: ReturnType<typeof setInterval> | null = null;
  let lastSampleAt = 0;
  let watchedRemainder = 0;

  function currentPosition(): number {
    return Math.max(0, Math.floor(player?.getCurrentTime() ?? 0));
  }

  function sampleWatchTime(): void {
    if (!player || lastSampleAt === 0) return;

    const now = performance.now();
    const elapsedSeconds = Math.min((now - lastSampleAt) / 1000, 2);
    lastSampleAt = now;
    watchedRemainder += elapsedSeconds;

    const watchedSeconds = Math.floor(watchedRemainder);
    if (watchedSeconds < 1) return;

    watchedRemainder -= watchedSeconds;
    options.onWatchSample(currentPosition(), watchedSeconds);
  }

  function stopSampling(flush: boolean): void {
    if (sampleTimer) clearInterval(sampleTimer);
    sampleTimer = null;
    if (flush) sampleWatchTime();
    lastSampleAt = 0;
    if (player) options.onPlaybackStopped(currentPosition());
  }

  function handleStateChange(event: YouTubePlayerEvent): void {
    if (event.data === YOUTUBE_PLAYER_STATE.playing) {
      if (sampleTimer) return;
      lastSampleAt = performance.now();
      sampleTimer = setInterval(sampleWatchTime, 1000);
      return;
    }

    if (
      event.data === YOUTUBE_PLAYER_STATE.paused ||
      event.data === YOUTUBE_PLAYER_STATE.ended
    ) {
      stopSampling(true);
    }
  }

  async function createPlayer(): Promise<void> {
    if (!options.container.value || !options.videoId.value) return;

    try {
      await loadYouTubeApi();
      if (!options.container.value || !window.YT?.Player) return;

      player?.destroy();
      player = new window.YT.Player(options.container.value, {
        videoId: options.videoId.value,
        playerVars: { origin: window.location.origin, rel: 0 },
        events: {
          onReady: (event) => {
            if (options.startSeconds.value > 0) {
              event.target.seekTo(options.startSeconds.value, true);
            }
          },
          onStateChange: handleStateChange,
        },
      });
    } catch (error: unknown) {
      options.onError(
        error instanceof Error ? error.message : "YouTube Player gagal dimuat.",
      );
    }
  }

  onMounted(() => void createPlayer());
  watch(options.videoId, () => void createPlayer());
  onBeforeUnmount(() => {
    stopSampling(true);
    player?.destroy();
    player = null;
  });
}
