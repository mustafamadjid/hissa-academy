export const YOUTUBE_PLAYER_STATE = {
  ended: 0,
  playing: 1,
  paused: 2,
} as const;

export interface YouTubePlayerEvent {
  target: YouTubePlayer;
  data: number;
}

export interface YouTubePlayer {
  destroy(): void;
  getCurrentTime(): number;
  seekTo(seconds: number, allowSeekAhead: boolean): void;
}

export interface YouTubePlayerOptions {
  videoId: string;
  playerVars: {
    origin: string;
    rel: 0;
  };
  events: {
    onReady(event: YouTubePlayerEvent): void;
    onStateChange(event: YouTubePlayerEvent): void;
  };
}

export interface YouTubePlayerConstructor {
  new (element: HTMLElement, options: YouTubePlayerOptions): YouTubePlayer;
}

declare global {
  interface Window {
    YT?: {
      Player: YouTubePlayerConstructor;
    };
    onYouTubeIframeAPIReady?: () => void;
  }
}
