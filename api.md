# HISSA Academy API Specification

**Version:** 1.0  
**Base URL:** `/api/v1`  
**Authentication:** Laravel Sanctum  
**Content Type:** `application/json`  
**Identifier Policy:** Semua endpoint privat menggunakan `public_uuid`. Numeric primary key tidak diekspos ke client.

---

## 1. Response Convention

### 1.1 Success Response

```json
{
  "success": true,
  "message": "Request berhasil diproses.",
  "data": {}
}
```

### 1.2 Collection Response

```json
{
  "success": true,
  "message": "Data berhasil diambil.",
  "data": [],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  },
  "links": {
    "first": "/api/v1/resources?page=1",
    "last": "/api/v1/resources?page=7",
    "prev": null,
    "next": "/api/v1/resources?page=2"
  }
}
```

### 1.3 Error Response

```json
{
  "success": false,
  "message": "Request tidak dapat diproses.",
  "error": {
    "code": "VALIDATION_ERROR",
    "details": {}
  }
}
```

### 1.4 Validation Error

```json
{
  "success": false,
  "message": "Data yang diberikan tidak valid.",
  "error": {
    "code": "VALIDATION_ERROR",
    "details": {
      "email": [
        "Email wajib diisi."
      ]
    }
  }
}
```

### 1.5 HTTP Status Code

| Status | Penggunaan |
|---|---|
| `200 OK` | Read atau update berhasil |
| `201 Created` | Resource berhasil dibuat |
| `204 No Content` | Logout atau delete berhasil tanpa response body |
| `401 Unauthorized` | User belum terautentikasi |
| `403 Forbidden` | Role, ownership, atau prerequisite tidak terpenuhi |
| `404 Not Found` | Resource tidak ditemukan atau disamarkan karena ownership |
| `409 Conflict` | Unique constraint atau idempotency conflict |
| `422 Unprocessable Entity` | Validation error |
| `429 Too Many Requests` | Rate limit tercapai |

---

# 2. Authentication

## 2.1 Login

```http
POST /api/v1/auth/login
```

### Authentication

Public.

### Request

```json
{
  "email": "student@example.com",
  "password": "secret123"
}
```

### Response `200 OK`

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "user": {
      "uuid": "b6647f36-2417-4e3a-b148-6af4ff453fcf",
      "username": "student01",
      "email": "student@example.com",
      "phone": "081234567890",
      "full_name": "Student HISSA",
      "birth_date": "2002-04-10",
      "avatar_url": null,
      "role": {
        "name": "student"
      },
      "last_login_at": "2026-06-18T05:20:30Z"
    },
    "token": "1|example-sanctum-token"
  }
}
```

### Response `401 Unauthorized`

```json
{
  "success": false,
  "message": "Email atau password tidak valid.",
  "error": {
    "code": "INVALID_CREDENTIALS",
    "details": {}
  }
}
```

> Untuk Sanctum SPA berbasis cookie, field `token` dapat dihilangkan.

---

## 2.2 Logout

```http
POST /api/v1/auth/logout
```

### Authentication

Authenticated user.

### Response `204 No Content`

Tidak memiliki response body.

---

## 2.3 Get Current User

```http
GET /api/v1/auth/me
```

### Authentication

Authenticated user.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Profil user berhasil diambil.",
  "data": {
    "uuid": "b6647f36-2417-4e3a-b148-6af4ff453fcf",
    "username": "student01",
    "email": "student@example.com",
    "phone": "081234567890",
    "full_name": "Student HISSA",
    "birth_date": "2002-04-10",
    "avatar_url": null,
    "role": {
      "name": "student"
    },
    "last_login_at": "2026-06-18T05:20:30Z"
  }
}
```

---

## 2.4 Google OAuth Redirect

```http
GET /auth/google/redirect
```

### Authentication

Public.

### Response

Redirect ke halaman autentikasi Google.

---

## 2.5 Google OAuth Callback

```http
GET /auth/google/callback
```

### Authentication

Public.

### Response

Redirect ke frontend setelah autentikasi berhasil atau gagal.

Contoh redirect berhasil:

```text
https://app.hissa.id/auth/callback?status=success
```

---

# 3. Student Courses

## 3.1 Get Published Courses

```http
GET /api/v1/courses
```

### Authentication

Authenticated student.

### Query Parameters

| Parameter | Type | Required | Description |
|---|---:|---:|---|
| `page` | integer | No | Halaman data |
| `per_page` | integer | No | Jumlah data per halaman |
| `search` | string | No | Pencarian nama course |

### Response `200 OK`

```json
{
  "success": true,
  "message": "Daftar course berhasil diambil.",
  "data": [
    {
      "uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
      "name": "Dasar Investasi Saham Syariah",
      "description": "Pengenalan investasi dan screening saham syariah.",
      "minimum_score": 75,
      "status": "published",
      "progress": {
        "completed_lessons": 2,
        "required_lessons": 5,
        "percentage": 40,
        "status": "in_progress"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  },
  "links": {
    "first": "/api/v1/courses?page=1",
    "last": "/api/v1/courses?page=1",
    "prev": null,
    "next": null
  }
}
```

---

## 3.2 Get Course Detail

```http
GET /api/v1/courses/{course_uuid}
```

### Authentication

Authenticated student.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Detail course berhasil diambil.",
  "data": {
    "uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
    "name": "Dasar Investasi Saham Syariah",
    "description": "Pengenalan investasi dan screening saham syariah.",
    "minimum_score": 75,
    "status": "published",
    "progress": {
      "completed_lessons": 1,
      "required_lessons": 3,
      "percentage": 33.33,
      "status": "in_progress"
    },
    "lessons": [
      {
        "uuid": "debc0bbb-c826-4f1d-aed2-6a09f083cb25",
        "title": "Pengenalan Saham Syariah",
        "position": 1,
        "is_required": true,
        "state": "completed",
        "is_locked": false
      },
      {
        "uuid": "032f2446-1f50-4380-90e8-db26c275c518",
        "title": "Prinsip Screening Saham",
        "position": 2,
        "is_required": true,
        "state": "available",
        "is_locked": false
      },
      {
        "uuid": "7983315d-5a77-414a-8da5-ebcb8a664aa2",
        "title": "Studi Kasus Screening",
        "position": 3,
        "is_required": true,
        "state": "locked",
        "is_locked": true
      }
    ],
    "quiz": {
      "available": false,
      "reason": "REQUIRED_LESSONS_NOT_COMPLETED"
    }
  }
}
```

### Response `404 Not Found`

```json
{
  "success": false,
  "message": "Course tidak ditemukan.",
  "error": {
    "code": "COURSE_NOT_FOUND",
    "details": {}
  }
}
```

---

## 3.3 Get Course Progress

```http
GET /api/v1/courses/{course_uuid}/progress
```

### Authentication

Authenticated student.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Progress course berhasil diambil.",
  "data": {
    "course_uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
    "completed_lessons": 2,
    "required_lessons": 5,
    "percentage": 40,
    "status": "in_progress",
    "quiz_unlocked": false,
    "lessons": [
      {
        "lesson_uuid": "debc0bbb-c826-4f1d-aed2-6a09f083cb25",
        "position": 1,
        "status": "completed",
        "percentage": 92.5,
        "completed_at": "2026-06-18T05:40:00Z"
      }
    ]
  }
}
```

---

# 4. Student Lessons and Progress

## 4.1 Get Lesson Detail

```http
GET /api/v1/lessons/{lesson_uuid}
```

### Authentication

Authenticated student.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Detail lesson berhasil diambil.",
  "data": {
    "uuid": "032f2446-1f50-4380-90e8-db26c275c518",
    "course": {
      "uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
      "name": "Dasar Investasi Saham Syariah"
    },
    "title": "Prinsip Screening Saham",
    "position": 2,
    "is_required": true,
    "state": "in_progress",
    "video": {
      "youtube_video_id": "dQw4w9WgXcQ",
      "duration_seconds": 600
    },
    "progress": {
      "last_position_seconds": 215,
      "total_watched_seconds": 180,
      "percentage": 30,
      "status": "in_progress",
      "completed_at": null
    },
    "navigation": {
      "previous_lesson_uuid": "debc0bbb-c826-4f1d-aed2-6a09f083cb25",
      "next_lesson_uuid": null,
      "can_continue": false
    }
  }
}
```

### Response `403 Forbidden`

```json
{
  "success": false,
  "message": "Lesson masih terkunci.",
  "error": {
    "code": "LESSON_LOCKED",
    "details": {
      "required_previous_lesson_uuid": "debc0bbb-c826-4f1d-aed2-6a09f083cb25"
    }
  }
}
```

---

## 4.2 Get Lesson Progress

```http
GET /api/v1/lessons/{lesson_uuid}/progress
```

### Authentication

Authenticated student.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Progress lesson berhasil diambil.",
  "data": {
    "lesson_uuid": "032f2446-1f50-4380-90e8-db26c275c518",
    "last_position_seconds": 215,
    "total_watched_seconds": 430,
    "duration_seconds": 600,
    "percentage": 71.67,
    "status": "in_progress",
    "completed_at": null,
    "can_continue": false
  }
}
```

---

## 4.3 Submit Progress Heartbeat

```http
POST /api/v1/lessons/{lesson_uuid}/progress
```

### Authentication

Authenticated student.

### Request

```json
{
  "current_position_seconds": 215,
  "player_state": "playing",
  "client_event_at": "2026-06-18T05:20:30Z"
}
```

Nilai `player_state` yang diperbolehkan:

```text
playing
paused
ended
buffering
```

Client tidak diperbolehkan mengirim:

```text
user_id
total_watched_seconds
percentage
status
completed
completed_at
```

### Response `200 OK`

```json
{
  "success": true,
  "message": "Progress berhasil diperbarui.",
  "data": {
    "lesson_uuid": "032f2446-1f50-4380-90e8-db26c275c518",
    "last_position_seconds": 215,
    "total_watched_seconds": 540,
    "duration_seconds": 600,
    "percentage": 90,
    "status": "completed",
    "completed_at": "2026-06-18T05:20:30Z",
    "can_continue": true,
    "next_lesson": {
      "uuid": "7983315d-5a77-414a-8da5-ebcb8a664aa2",
      "is_unlocked": true
    }
  }
}
```

### Response `422 Unprocessable Entity`

```json
{
  "success": false,
  "message": "Data heartbeat tidak valid.",
  "error": {
    "code": "INVALID_PROGRESS_HEARTBEAT",
    "details": {
      "current_position_seconds": [
        "Posisi video tidak boleh melebihi durasi video."
      ]
    }
  }
}
```

### Response `429 Too Many Requests`

```json
{
  "success": false,
  "message": "Heartbeat dikirim terlalu sering.",
  "error": {
    "code": "PROGRESS_RATE_LIMITED",
    "details": {
      "retry_after_seconds": 3
    }
  }
}
```

---

# 5. Student Quiz

## 5.1 Get Course Quiz

```http
GET /api/v1/courses/{course_uuid}/quiz
```

### Authentication

Authenticated student.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Quiz berhasil diambil.",
  "data": {
    "uuid": "5e7f2472-31ef-4586-be20-a4b364c3e889",
    "name": "Quiz Akhir Dasar Saham Syariah",
    "is_active": true,
    "minimum_score": 75,
    "total_questions": 10,
    "attempt_policy": {
      "maximum_attempts": null,
      "attempts_used": 1,
      "attempts_remaining": null
    }
  }
}
```

### Response `403 Forbidden`

```json
{
  "success": false,
  "message": "Quiz belum dapat diakses.",
  "error": {
    "code": "QUIZ_LOCKED",
    "details": {
      "reason": "REQUIRED_LESSONS_NOT_COMPLETED"
    }
  }
}
```

---

## 5.2 Create Quiz Attempt

```http
POST /api/v1/quizzes/{quiz_uuid}/attempts
```

### Authentication

Authenticated student.

### Request

```json
{}
```

### Response `201 Created`

```json
{
  "success": true,
  "message": "Quiz attempt berhasil dibuat.",
  "data": {
    "uuid": "09e20716-852f-4dd0-860a-b59900aa3849",
    "quiz": {
      "uuid": "5e7f2472-31ef-4586-be20-a4b364c3e889",
      "name": "Quiz Akhir Dasar Saham Syariah"
    },
    "status": "in_progress",
    "started_at": "2026-06-18T06:00:00Z",
    "submitted_at": null,
    "questions": [
      {
        "uuid": "fb844467-5563-443a-a30a-394efc024071",
        "question_text": "Apa tujuan screening saham syariah?",
        "points": 10,
        "position": 1,
        "options": [
          {
            "uuid": "46a188be-a829-4ea8-b604-73bded8ccbae",
            "option_text": "Menentukan kesesuaian saham dengan prinsip syariah",
            "position": 1
          },
          {
            "uuid": "7cf5950d-5336-4bdc-8568-7777b934158b",
            "option_text": "Menjamin keuntungan investasi",
            "position": 2
          }
        ]
      }
    ]
  }
}
```

> Response tidak boleh mengandung `is_correct` atau indikator jawaban benar.

### Response `409 Conflict`

```json
{
  "success": false,
  "message": "Masih terdapat quiz attempt yang aktif.",
  "error": {
    "code": "ACTIVE_ATTEMPT_EXISTS",
    "details": {
      "attempt_uuid": "09e20716-852f-4dd0-860a-b59900aa3849"
    }
  }
}
```

---

## 5.3 Get Quiz Attempt

```http
GET /api/v1/quiz-attempts/{attempt_uuid}
```

### Authentication

Authenticated student and attempt owner.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Quiz attempt berhasil diambil.",
  "data": {
    "uuid": "09e20716-852f-4dd0-860a-b59900aa3849",
    "quiz": {
      "uuid": "5e7f2472-31ef-4586-be20-a4b364c3e889",
      "name": "Quiz Akhir Dasar Saham Syariah"
    },
    "status": "in_progress",
    "score": null,
    "started_at": "2026-06-18T06:00:00Z",
    "submitted_at": null,
    "questions": [
      {
        "uuid": "fb844467-5563-443a-a30a-394efc024071",
        "question_text": "Apa tujuan screening saham syariah?",
        "points": 10,
        "position": 1,
        "selected_option_uuid": null,
        "options": [
          {
            "uuid": "46a188be-a829-4ea8-b604-73bded8ccbae",
            "option_text": "Menentukan kesesuaian saham dengan prinsip syariah",
            "position": 1
          }
        ]
      }
    ]
  }
}
```

---

## 5.4 Submit Quiz Attempt

```http
POST /api/v1/quiz-attempts/{attempt_uuid}/submit
```

### Authentication

Authenticated student and attempt owner.

### Request

```json
{
  "answers": [
    {
      "question_uuid": "fb844467-5563-443a-a30a-394efc024071",
      "selected_option_uuid": "46a188be-a829-4ea8-b604-73bded8ccbae"
    },
    {
      "question_uuid": "953fc31c-d8c4-4ca3-8455-cbe785195383",
      "selected_option_uuid": "112cce74-fb38-4667-bad6-e86d741d5394"
    }
  ]
}
```

Client tidak diperbolehkan mengirim:

```text
score
status
passed
is_correct
certificate_uuid
```

### Response `200 OK` — Passed

```json
{
  "success": true,
  "message": "Quiz berhasil disubmit dan user dinyatakan lulus.",
  "data": {
    "attempt_uuid": "09e20716-852f-4dd0-860a-b59900aa3849",
    "score": 80,
    "minimum_score": 75,
    "status": "passed",
    "started_at": "2026-06-18T06:00:00Z",
    "submitted_at": "2026-06-18T06:20:00Z",
    "result": {
      "correct_answers": 8,
      "incorrect_answers": 2,
      "total_questions": 10
    },
    "certificate": {
      "uuid": "f00ce01b-20df-468d-8ce9-52929c922fad",
      "certificate_number": "HISSA-2026-X7KD9Q2M",
      "status": "valid",
      "issued_at": "2026-06-18T06:20:05Z"
    }
  }
}
```

### Response `200 OK` — Failed

```json
{
  "success": true,
  "message": "Quiz berhasil disubmit, tetapi nilai belum memenuhi batas kelulusan.",
  "data": {
    "attempt_uuid": "09e20716-852f-4dd0-860a-b59900aa3849",
    "score": 60,
    "minimum_score": 75,
    "status": "failed",
    "started_at": "2026-06-18T06:00:00Z",
    "submitted_at": "2026-06-18T06:20:00Z",
    "result": {
      "correct_answers": 6,
      "incorrect_answers": 4,
      "total_questions": 10
    },
    "certificate": null
  }
}
```

### Response `409 Conflict`

```json
{
  "success": false,
  "message": "Quiz attempt sudah pernah disubmit.",
  "error": {
    "code": "ATTEMPT_ALREADY_SUBMITTED",
    "details": {}
  }
}
```

---

# 6. Student Certificates

## 6.1 Get User Certificates

```http
GET /api/v1/certificates
```

### Authentication

Authenticated student.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Daftar sertifikat berhasil diambil.",
  "data": [
    {
      "uuid": "f00ce01b-20df-468d-8ce9-52929c922fad",
      "certificate_number": "HISSA-2026-X7KD9Q2M",
      "course": {
        "uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
        "name": "Dasar Investasi Saham Syariah"
      },
      "issued_at": "2026-06-18T06:20:05Z",
      "status": "valid"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  },
  "links": {
    "first": "/api/v1/certificates?page=1",
    "last": "/api/v1/certificates?page=1",
    "prev": null,
    "next": null
  }
}
```

---

## 6.2 Get Certificate Detail

```http
GET /api/v1/certificates/{certificate_uuid}
```

### Authentication

Authenticated certificate owner.

### Response `200 OK`

```json
{
  "success": true,
  "message": "Detail sertifikat berhasil diambil.",
  "data": {
    "uuid": "f00ce01b-20df-468d-8ce9-52929c922fad",
    "certificate_number": "HISSA-2026-X7KD9Q2M",
    "participant_name": "Student HISSA",
    "course": {
      "uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
      "name": "Dasar Investasi Saham Syariah"
    },
    "issued_at": "2026-06-18T06:20:05Z",
    "status": "valid",
    "verification_url": "https://app.hissa.id/verify/HISSA-2026-X7KD9Q2M",
    "download_url": "/api/v1/certificates/f00ce01b-20df-468d-8ce9-52929c922fad/download"
  }
}
```

---

## 6.3 Download Certificate PDF

```http
GET /api/v1/certificates/{certificate_uuid}/download
```

### Authentication

Authenticated certificate owner.

### Response `200 OK`

```http
Content-Type: application/pdf
Content-Disposition: attachment; filename="HISSA-2026-X7KD9Q2M.pdf"
```

Response berupa binary PDF, bukan JSON.

### Response `404 Not Found`

```json
{
  "success": false,
  "message": "File sertifikat tidak ditemukan.",
  "error": {
    "code": "CERTIFICATE_FILE_NOT_FOUND",
    "details": {}
  }
}
```

---

# 7. Public Certificate Verification

## 7.1 Verification Page

```http
GET /verify/{certificate_number}
```

### Authentication

Public.

### Response

Halaman web publik yang menampilkan hasil verifikasi sertifikat.

---

## 7.2 Verify Certificate API

```http
GET /api/v1/public/certificates/{certificate_number}
```

### Authentication

Public.

### Response `200 OK` — Valid

```json
{
  "success": true,
  "message": "Sertifikat valid.",
  "data": {
    "verification_status": "valid",
    "certificate_number": "HISSA-2026-X7KD9Q2M",
    "participant_name": "Student HISSA",
    "course_name": "Dasar Investasi Saham Syariah",
    "issued_at": "2026-06-18T06:20:05Z"
  }
}
```

### Response `200 OK` — Revoked

```json
{
  "success": true,
  "message": "Sertifikat telah dicabut.",
  "data": {
    "verification_status": "revoked",
    "certificate_number": "HISSA-2026-X7KD9Q2M",
    "participant_name": "Student HISSA",
    "course_name": "Dasar Investasi Saham Syariah",
    "issued_at": "2026-06-18T06:20:05Z"
  }
}
```

### Response `404 Not Found`

```json
{
  "success": false,
  "message": "Sertifikat tidak ditemukan.",
  "error": {
    "code": "CERTIFICATE_NOT_FOUND",
    "details": {
      "verification_status": "not_found"
    }
  }
}
```

Endpoint publik tidak boleh menampilkan:

```text
email
user_id
user_uuid
certificate_uuid
pdf_path
revoked_by
```

---

# 8. Admin Courses

## 8.1 Create Course

```http
POST /api/v1/admin/courses
```

### Authentication

Authenticated admin.

### Request

```json
{
  "course_name": "Dasar Investasi Saham Syariah",
  "description": "Pengenalan investasi dan screening saham syariah.",
  "minimum_score": 75,
  "status": "draft"
}
```

### Response `201 Created`

```json
{
  "success": true,
  "message": "Course berhasil dibuat.",
  "data": {
    "uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
    "course_name": "Dasar Investasi Saham Syariah",
    "description": "Pengenalan investasi dan screening saham syariah.",
    "minimum_score": 75,
    "status": "draft",
    "created_at": "2026-06-18T07:00:00Z",
    "updated_at": "2026-06-18T07:00:00Z"
  }
}
```

---

## 8.2 Update Course

```http
PATCH /api/v1/admin/courses/{course_uuid}
```

### Authentication

Authenticated admin.

### Request

```json
{
  "description": "Deskripsi course yang telah diperbarui.",
  "minimum_score": 80,
  "status": "published"
}
```

### Response `200 OK`

```json
{
  "success": true,
  "message": "Course berhasil diperbarui.",
  "data": {
    "uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
    "course_name": "Dasar Investasi Saham Syariah",
    "description": "Deskripsi course yang telah diperbarui.",
    "minimum_score": 80,
    "status": "published",
    "updated_at": "2026-06-18T07:10:00Z"
  }
}
```

---

# 9. Admin Lessons and Videos

## 9.1 Create Lesson

```http
POST /api/v1/admin/courses/{course_uuid}/lessons
```

### Authentication

Authenticated admin.

### Request

```json
{
  "title": "Pengenalan Saham Syariah",
  "position": 1,
  "is_required": true
}
```

### Response `201 Created`

```json
{
  "success": true,
  "message": "Lesson berhasil dibuat.",
  "data": {
    "uuid": "debc0bbb-c826-4f1d-aed2-6a09f083cb25",
    "course_uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
    "title": "Pengenalan Saham Syariah",
    "position": 1,
    "is_required": true,
    "created_at": "2026-06-18T07:20:00Z"
  }
}
```

### Response `409 Conflict`

```json
{
  "success": false,
  "message": "Position lesson sudah digunakan pada course ini.",
  "error": {
    "code": "LESSON_POSITION_CONFLICT",
    "details": {
      "position": 1
    }
  }
}
```

---

## 9.2 Update Lesson

```http
PATCH /api/v1/admin/lessons/{lesson_uuid}
```

### Authentication

Authenticated admin.

### Request

```json
{
  "title": "Pengenalan dan Prinsip Saham Syariah",
  "position": 1,
  "is_required": true
}
```

### Response `200 OK`

```json
{
  "success": true,
  "message": "Lesson berhasil diperbarui.",
  "data": {
    "uuid": "debc0bbb-c826-4f1d-aed2-6a09f083cb25",
    "title": "Pengenalan dan Prinsip Saham Syariah",
    "position": 1,
    "is_required": true,
    "updated_at": "2026-06-18T07:30:00Z"
  }
}
```

---

## 9.3 Delete Lesson

```http
DELETE /api/v1/admin/lessons/{lesson_uuid}
```

### Authentication

Authenticated admin.

### Response `204 No Content`

Tidak memiliki response body.

### Response `409 Conflict`

```json
{
  "success": false,
  "message": "Lesson tidak dapat dihapus karena sudah memiliki data progress.",
  "error": {
    "code": "LESSON_HAS_DEPENDENCIES",
    "details": {
      "dependencies": [
        "user_progress"
      ]
    }
  }
}
```

---

## 9.4 Create or Update Lesson Video

```http
PUT /api/v1/admin/lessons/{lesson_uuid}/video
```

### Authentication

Authenticated admin.

### Request

```json
{
  "youtube_video_id": "dQw4w9WgXcQ"
}
```

### Response `200 OK`

```json
{
  "success": true,
  "message": "Video lesson berhasil disimpan.",
  "data": {
    "uuid": "a559678e-301c-444a-a5c7-a56bd93774f2",
    "lesson_uuid": "debc0bbb-c826-4f1d-aed2-6a09f083cb25",
    "youtube_video_id": "dQw4w9WgXcQ",
    "duration_seconds": 600,
    "updated_at": "2026-06-18T07:40:00Z"
  }
}
```

### Response `422 Unprocessable Entity`

```json
{
  "success": false,
  "message": "Video YouTube tidak valid atau tidak dapat diakses.",
  "error": {
    "code": "INVALID_YOUTUBE_VIDEO",
    "details": {
      "youtube_video_id": [
        "Video tidak ditemukan, bersifat private, atau tidak dapat di-embed."
      ]
    }
  }
}
```

---

# 10. Admin Quiz

## 10.1 Create or Update Course Quiz

```http
PUT /api/v1/admin/courses/{course_uuid}/quiz
```

### Authentication

Authenticated admin.

### Request

```json
{
  "quiz_name": "Quiz Akhir Dasar Saham Syariah",
  "is_active": true
}
```

### Response `200 OK`

```json
{
  "success": true,
  "message": "Quiz berhasil disimpan.",
  "data": {
    "uuid": "5e7f2472-31ef-4586-be20-a4b364c3e889",
    "course_uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
    "quiz_name": "Quiz Akhir Dasar Saham Syariah",
    "is_active": true,
    "updated_at": "2026-06-18T08:00:00Z"
  }
}
```

---

# 11. Admin Certificates

## 11.1 Get Certificates

```http
GET /api/v1/admin/certificates
```

### Authentication

Authenticated admin.

### Query Parameters

| Parameter | Type | Required | Description |
|---|---:|---:|---|
| `page` | integer | No | Halaman data |
| `per_page` | integer | No | Jumlah data per halaman |
| `status` | string | No | `valid` atau `revoked` |
| `search` | string | No | Nama user atau certificate number |
| `course_uuid` | UUID | No | Filter course |

### Response `200 OK`

```json
{
  "success": true,
  "message": "Daftar sertifikat berhasil diambil.",
  "data": [
    {
      "uuid": "f00ce01b-20df-468d-8ce9-52929c922fad",
      "certificate_number": "HISSA-2026-X7KD9Q2M",
      "participant": {
        "uuid": "b6647f36-2417-4e3a-b148-6af4ff453fcf",
        "full_name": "Student HISSA",
        "email": "student@example.com"
      },
      "course": {
        "uuid": "51fd6afe-cd31-49e3-b83b-4bfc84c1a7c2",
        "name": "Dasar Investasi Saham Syariah"
      },
      "issued_at": "2026-06-18T06:20:05Z",
      "status": "valid"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  },
  "links": {
    "first": "/api/v1/admin/certificates?page=1",
    "last": "/api/v1/admin/certificates?page=1",
    "prev": null,
    "next": null
  }
}
```

---

## 11.2 Revoke Certificate

```http
PATCH /api/v1/admin/certificates/{certificate_uuid}/revoke
```

### Authentication

Authenticated admin.

### Request

```json
{
  "reason": "Sertifikat diterbitkan berdasarkan data yang tidak valid."
}
```

### Response `200 OK`

```json
{
  "success": true,
  "message": "Sertifikat berhasil dicabut.",
  "data": {
    "uuid": "f00ce01b-20df-468d-8ce9-52929c922fad",
    "certificate_number": "HISSA-2026-X7KD9Q2M",
    "status": "revoked",
    "revoked_at": "2026-06-18T09:00:00Z",
    "revocation_reason": "Sertifikat diterbitkan berdasarkan data yang tidak valid."
  }
}
```

### Response `409 Conflict`

```json
{
  "success": false,
  "message": "Sertifikat sudah dicabut sebelumnya.",
  "error": {
    "code": "CERTIFICATE_ALREADY_REVOKED",
    "details": {}
  }
}
```

---

# 12. Recommended Additional Admin APIs

Endpoint berikut belum tercantum dalam PRD v2, tetapi dibutuhkan agar pengelolaan course dan quiz dapat dilakukan secara lengkap.

## 12.1 Get Admin Courses

```http
GET /api/v1/admin/courses
```

## 12.2 Get Admin Course Detail

```http
GET /api/v1/admin/courses/{course_uuid}
```

## 12.3 Create Quiz Question

```http
POST /api/v1/admin/quizzes/{quiz_uuid}/questions
```

### Request

```json
{
  "question_text": "Apa tujuan screening saham syariah?",
  "points": 10,
  "position": 1,
  "options": [
    {
      "option_text": "Menentukan kesesuaian saham dengan prinsip syariah",
      "is_correct": true,
      "position": 1
    },
    {
      "option_text": "Menjamin keuntungan investasi",
      "is_correct": false,
      "position": 2
    }
  ]
}
```

### Response `201 Created`

```json
{
  "success": true,
  "message": "Pertanyaan quiz berhasil dibuat.",
  "data": {
    "uuid": "fb844467-5563-443a-a30a-394efc024071",
    "question_text": "Apa tujuan screening saham syariah?",
    "points": 10,
    "position": 1,
    "options": [
      {
        "uuid": "46a188be-a829-4ea8-b604-73bded8ccbae",
        "option_text": "Menentukan kesesuaian saham dengan prinsip syariah",
        "is_correct": true,
        "position": 1
      },
      {
        "uuid": "7cf5950d-5336-4bdc-8568-7777b934158b",
        "option_text": "Menjamin keuntungan investasi",
        "is_correct": false,
        "position": 2
      }
    ]
  }
}
```

## 12.4 Update Quiz Question

```http
PATCH /api/v1/admin/quiz-questions/{question_uuid}
```

## 12.5 Delete Quiz Question

```http
DELETE /api/v1/admin/quiz-questions/{question_uuid}
```

---

# 13. Security Rules

1. `user_id` selalu diambil dari authentication context.
2. Client tidak boleh menetapkan `status`, `score`, `completed_at`, `issued_at`, atau role.
3. Semua resource privat wajib memeriksa ownership dan authorization.
4. Numeric primary key tidak boleh dikirim dalam response.
5. Endpoint progress, login, quiz submit, dan public verification wajib menggunakan rate limiting.
6. File sertifikat disimpan pada private storage.
7. `pdf_path` tidak boleh dikirim ke client.
8. Quiz response untuk student tidak boleh membocorkan `is_correct`.
9. Penerbitan sertifikat harus idempotent berdasarkan kombinasi user dan course.
10. Endpoint verification hanya mengembalikan metadata minimum.

---

# 14. Main Error Codes

| Error Code | HTTP Status | Description |
|---|---:|---|
| `INVALID_CREDENTIALS` | 401 | Email atau password tidak valid |
| `UNAUTHENTICATED` | 401 | User belum login |
| `FORBIDDEN` | 403 | User tidak memiliki izin |
| `COURSE_NOT_FOUND` | 404 | Course tidak ditemukan |
| `LESSON_NOT_FOUND` | 404 | Lesson tidak ditemukan |
| `LESSON_LOCKED` | 403 | Lesson belum dapat diakses |
| `QUIZ_LOCKED` | 403 | Quiz belum dapat diakses |
| `ACTIVE_ATTEMPT_EXISTS` | 409 | Masih terdapat attempt aktif |
| `ATTEMPT_ALREADY_SUBMITTED` | 409 | Attempt sudah disubmit |
| `CERTIFICATE_NOT_FOUND` | 404 | Sertifikat tidak ditemukan |
| `CERTIFICATE_ALREADY_REVOKED` | 409 | Sertifikat sudah dicabut |
| `LESSON_POSITION_CONFLICT` | 409 | Position lesson sudah digunakan |
| `LESSON_HAS_DEPENDENCIES` | 409 | Lesson memiliki data terkait |
| `INVALID_PROGRESS_HEARTBEAT` | 422 | Data heartbeat tidak valid |
| `PROGRESS_RATE_LIMITED` | 429 | Heartbeat dikirim terlalu sering |
| `INVALID_YOUTUBE_VIDEO` | 422 | Video YouTube tidak valid |
| `VALIDATION_ERROR` | 422 | Request validation gagal |
| `INTERNAL_SERVER_ERROR` | 500 | Kesalahan internal server |
