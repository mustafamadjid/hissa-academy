const YOUTUBE_VIDEO_ID_PATTERN = /^[A-Za-z0-9_-]{6,20}$/
const YOUTUBE_EMBED_BASE_URL = 'https://www.youtube.com/embed/'

export function extractYoutubeVideoId(value: string): string | null {
  const input = value.trim()

  if (YOUTUBE_VIDEO_ID_PATTERN.test(input)) return input

  let url: URL

  try {
    url = new URL(input)
  } catch {
    return null
  }

  const hostname = url.hostname.toLowerCase().replace(/^www\./, '')
  let candidate: string | null = null

  if (hostname === 'youtu.be') {
    candidate = url.pathname.split('/').filter(Boolean)[0] ?? null
  } else if (
    hostname === 'youtube.com' ||
    hostname === 'm.youtube.com' ||
    hostname === 'youtube-nocookie.com'
  ) {
    candidate = url.searchParams.get('v')

    if (!candidate) {
      const segments = url.pathname.split('/').filter(Boolean)
      const supportedPrefix = ['embed', 'shorts', 'live'].includes(segments[0] ?? '')
      candidate = supportedPrefix ? (segments[1] ?? null) : null
    }
  }

  return candidate && YOUTUBE_VIDEO_ID_PATTERN.test(candidate) ? candidate : null
}

export function generateYoutubeEmbedUrl(videoId: string): string | null {
  const normalizedVideoId = videoId.trim()

  if (!YOUTUBE_VIDEO_ID_PATTERN.test(normalizedVideoId)) return null

  return `${YOUTUBE_EMBED_BASE_URL}${normalizedVideoId}`
}
