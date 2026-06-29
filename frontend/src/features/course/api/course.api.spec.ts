import { beforeEach, describe, expect, it, vi } from 'vitest'

import { httpClient } from '@/shared/api/http-client'
import {
  createCourse,
  deleteCourse,
  getAdminCourses,
  updateCourse,
} from './course.api'
import type { CreateCourseRequest } from '../types/course.types'

vi.mock('@/shared/api/http-client', () => ({
  httpClient: {
    get: vi.fn(),
    post: vi.fn(),
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
})
