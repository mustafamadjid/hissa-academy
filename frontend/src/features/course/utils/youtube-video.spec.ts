import { describe, expect, it } from 'vitest'

import { extractYoutubeVideoId } from './youtube-video'

describe('extractYoutubeVideoId', () => {
  it.each([
    ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['https://youtu.be/dQw4w9WgXcQ?t=15', 'dQw4w9WgXcQ'],
    ['https://youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['https://youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
  ])('extracts an id from %s', (input, expected) => {
    expect(extractYoutubeVideoId(input)).toBe(expected)
  })

  it.each([
    'https://example.com/watch?v=dQw4w9WgXcQ',
    'https://youtube.com/watch?v=bad!',
    'not a youtube link',
  ])('rejects invalid input %s', (input) => {
    expect(extractYoutubeVideoId(input)).toBeNull()
  })
})
