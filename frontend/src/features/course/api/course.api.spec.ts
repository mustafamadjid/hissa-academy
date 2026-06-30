import { beforeEach, describe, expect, it, vi } from 'vitest'

import { httpClient } from '@/shared/api/http-client'
import {
  createCourse,
  createAdminCourseLesson,
  deleteAdminLesson,
  deleteAdminLessonVideo,
  deleteCourse,
  getAdminCourseDetail,
  getAdminCourseLessons,
  getAdminCourses,
  reorderAdminCourseLessons,
  updateAdminLesson,
  updateCourse,
  upsertAdminLessonVideo,
} from './course.api'
import type { CreateCourseRequest } from '../types/course.types'

vi.mock('@/shared/api/http-client', () => ({
  httpClient: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    patch: vi.fn(),
    delete: vi.fn(),
  },
}))

const payload: CreateCourseRequest = {
  course_name: 'Dasar Perbankan Syariah',
  description: 'Pengenalan konsep perbankan syariah.',
  minimum_score: 75,
  status: 'draft',
}

describe('course API', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('gets paginated admin courses with query parameters', async () => {
    vi.mocked(httpClient.get).mockResolvedValue({})
    const query = { page: 2, limit: 10, search: 'bank' }

    await getAdminCourses(query)

    expect(httpClient.get).toHaveBeenCalledWith('admin/courses', {
      params: query,
    })
  })

  it('gets an admin course detail by id', async () => {
    vi.mocked(httpClient.get).mockResolvedValue({})

    await getAdminCourseDetail('course-id')

    expect(httpClient.get).toHaveBeenCalledWith('admin/courses/course-id')
  })

  it('creates a course', async () => {
    vi.mocked(httpClient.post).mockResolvedValue({})

    await createCourse(payload)

    expect(httpClient.post).toHaveBeenCalledWith('admin/courses', payload)
  })

  it('updates a course with PATCH', async () => {
    vi.mocked(httpClient.patch).mockResolvedValue({})

    await updateCourse('course-id', { status: 'active' })

    expect(httpClient.patch).toHaveBeenCalledWith(
      'admin/courses/course-id',
      { status: 'active' },
    )
  })

  it('deletes a course', async () => {
    vi.mocked(httpClient.delete).mockResolvedValue({})

    await deleteCourse('course-id')

    expect(httpClient.delete).toHaveBeenCalledWith('admin/courses/course-id')
  })

  it('uses the admin lesson endpoints with their specified methods and payloads', async () => {
    vi.mocked(httpClient.get).mockResolvedValue({})
    vi.mocked(httpClient.patch).mockResolvedValue({})
    vi.mocked(httpClient.put).mockResolvedValue({})
    vi.mocked(httpClient.delete).mockResolvedValue({})
    const reorderPayload = {
      lessons: [{ id: 'lesson-id', position: 1 }],
    }
    const createPayload = {
      title: 'Lesson baru',
      youtube_video_id: 'dQw4w9WgXcQ',
      position: 1,
      is_required: true,
    }

    await getAdminCourseLessons('course-id')
    await createAdminCourseLesson('course-id', createPayload)
    await reorderAdminCourseLessons('course-id', reorderPayload)
    await updateAdminLesson('lesson-id', { title: 'Judul baru' })
    await upsertAdminLessonVideo('lesson-id', {
      youtube_video_id: 'dQw4w9WgXcQ',
    })
    await deleteAdminLessonVideo('lesson-id')
    await deleteAdminLesson('lesson-id')

    expect(httpClient.get).toHaveBeenCalledWith(
      'admin/courses/course-id/lessons',
    )
    expect(httpClient.post).toHaveBeenCalledWith(
      'admin/courses/course-id/lessons',
      createPayload,
    )
    expect(httpClient.patch).toHaveBeenCalledWith(
      'admin/courses/course-id/lessons/reorder',
      reorderPayload,
    )
    expect(httpClient.patch).toHaveBeenCalledWith(
      'admin/lessons/lesson-id',
      { title: 'Judul baru' },
    )
    expect(httpClient.put).toHaveBeenCalledWith(
      'admin/lessons/lesson-id/video',
      { youtube_video_id: 'dQw4w9WgXcQ' },
    )
    expect(httpClient.delete).toHaveBeenCalledWith(
      'admin/lessons/lesson-id/video',
    )
    expect(httpClient.delete).toHaveBeenCalledWith('admin/lessons/lesson-id')
  })
})
