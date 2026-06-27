# OpenAPI Backend Synchronization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make `openapi.yaml` a complete and accurate description of every implemented backend API route plus the public HTML certificate-verification route.

**Architecture:** Treat Laravel routes, Form Requests, Controllers, and API Resources as the contract source. Keep one OpenAPI 3.x file, use operation-specific domain schemas where backend projections differ, and reuse common parameters, pagination, and error responses through `components`.

**Tech Stack:** OpenAPI 3.x YAML, Laravel/PHP backend contract sources, Python/PyYAML for structural checks, Redocly CLI for OpenAPI semantic linting.

---

## File Structure

- Modify: `openapi.yaml` — paths, operations, parameters, bodies, responses, security, examples, and reusable component definitions.
- Read only: `backend/routes/api/v1/*.php` and `backend/routes/web.php` — canonical route paths, methods, and middleware.
- Read only: `backend/app/Features/**/Http/Requests/*.php` — input validation and defaults.
- Read only: `backend/app/Features/**/Http/Controllers/*.php` — response envelopes, status codes, and media types.
- Read only: `backend/app/Features/**/Http/Resources/*.php` — serialized response fields.

### Task 1: Establish a machine-checkable route baseline

**Files:**
- Read: `backend/routes/api/v1/*.php`
- Read: `backend/routes/web.php`
- Modify: `openapi.yaml`

- [ ] **Step 1: Capture backend operations**

Run:

```powershell
Set-Location backend
php artisan route:list --path=api --json
```

Expected: 46 API operations, counting `GET|HEAD` as `GET`, all rooted at `/api/v1`.

- [ ] **Step 2: Capture current OpenAPI operations**

Run a short PyYAML script that loads `openapi.yaml`, iterates `paths`, and prints each `(method, path)` for `get`, `post`, `put`, `patch`, and `delete`.

Expected baseline differences include:

```text
/auth/google/redirect -> /api/v1/auth/google/redirect
/auth/google/callback -> /api/v1/auth/google/callback
PUT /api/v1/admin/courses/{course_uuid}/quiz -> POST
missing PATCH and DELETE /api/v1/admin/quizzes/{quiz_uuid}
missing GET /api/v1/admin/quizzes/{quiz_uuid}/questions
missing four /api/v1/admin/students operations
```

- [ ] **Step 3: Correct path and method coverage**

Make the path map equal to the backend route map and retain the additional web operation:

```text
GET /verify/{certificate_number}
```

Use the exact backend parameter spellings, including `courseId` for `PATCH /api/v1/admin/courses/{courseId}/lessons/reorder`.

- [ ] **Step 4: Re-run the operation comparison**

Expected: no missing or extra `/api/v1/**` method/path pairs; `/verify/{certificate_number}` is the only documented operation outside `/api/v1/**`.

- [ ] **Step 5: Commit path coverage**

```powershell
git add openapi.yaml
git commit -m "docs: align OpenAPI route coverage with backend"
```

### Task 2: Synchronize authentication and student-facing contracts

**Files:**
- Modify: `openapi.yaml`
- Read: `backend/app/Features/Auth/Http/{Controllers,Requests}/*.php`
- Read: `backend/app/Features/{Course,Lesson,UserProgress,Quizz,Certificate}/Http/{Controllers,Requests,Resources}/*.php`

- [ ] **Step 1: Correct authentication operations**

Define these exact contracts:

- `POST /api/v1/auth/login`: JSON body requires `email` and `password`; `200` contains `success`, `message`, and `data.user`; invalid credentials are `422` with only `message`; Form Request validation is `422`; server failure is `500`.
- `POST /api/v1/auth/logout`: authenticated session endpoint; `200` contains only `message`; server failure is `500`.
- `GET /api/v1/auth/me`: authenticated endpoint returning the serialized User directly, without a success envelope.
- both Google OAuth endpoints return `302` redirects and have no JSON body.

- [ ] **Step 2: Correct student course and lesson operations**

Model list/detail projections separately:

```yaml
StudentCourseSummary:
  required: [id, name, description, minimum_score, status, total_lessons, completed_lessons, progress_percentage]
StudentCourseDetail:
  allOf:
    - $ref: '#/components/schemas/StudentCourseSummary'
    - type: object
      required: [lessons]
```

Document the course progress response as the exact service-produced object used by `StudentCourseController::progress`. Document student lesson detail fields from `StudentLessonResource`, including video and progress fields only when serialized.

- [ ] **Step 3: Correct lesson-progress heartbeat contracts**

For `POST /api/v1/lessons/{lesson_uuid}/progress`, derive required/nullable fields and numeric bounds from `LessonProgressHeartbeatRequest`. Make `GET` and `POST` response data match `UserProgressResource`, with real `404`, `403`, `422`, and `500` envelopes.

- [ ] **Step 4: Correct student quiz contracts**

Define separate safe student schemas that never expose `is_correct` before submission:

- course quiz and questions from `StudentQuizResource`, `StudentQuizQuestionResource`, and `StudentQuizOptionResource`;
- attempt detail from `QuizAttemptResource`, including selected answer state;
- submission body from `SubmitQuizAttemptRequest`, including nested `answers[].question_uuid` and `answers[].answer_uuid`;
- submit result from `QuizSubmitResultResource` and optional certificate from `CertificateIssuedResource`.

Document controller-specific conflict/unprocessable responses, including active-attempt and already-submitted conditions where implemented.

- [ ] **Step 5: Correct student and public certificate contracts**

Use separate list/detail schemas because `StudentCertificateResource` conditionally adds `participant_name`, `verification_url`, and `download_url`. Define:

- paginated JSON list and detail responses;
- PDF download as `application/pdf` with `type: string`, `format: binary`;
- public verification API using `PublicCertificateResource`;
- `/verify/{certificate_number}` as `text/html` for both `200` and `404`.

- [ ] **Step 6: Commit student-facing contracts**

```powershell
git add openapi.yaml
git commit -m "docs: synchronize student API contracts"
```

### Task 3: Synchronize admin request and response contracts

**Files:**
- Modify: `openapi.yaml`
- Read: `backend/app/Features/{Course,Lesson,LessonVideo,Quizz,Student,Certificate}/Http/{Controllers,Requests,Resources}/*.php`

- [ ] **Step 1: Correct admin course inputs and outputs**

Document list query fields and defaults exactly:

```text
search: nullable string, max 255
sort_by: course_name|minimum_score|status|created_at|updated_at, default created_at
sort_direction: asc|desc, default desc
limit: integer 1..100, default 10
page: integer >=1, default 1
```

`POST` requires `course_name`, `description`, `minimum_score`, and `status`; `PATCH` exposes the same properties without a top-level required list. Responses use `CourseResource` field names (`id`, `name`, `description`, `minimum_score`, `status`), not request field names.

- [ ] **Step 2: Correct admin lesson and video inputs and outputs**

Derive create, partial update, reorder-array, and video upsert schemas from their Form Requests. Ensure status codes match controllers (`201` on creation, `200` on update/delete) and the lesson/video response properties match `LessonResource` and `LessonVideoResource`.

- [ ] **Step 3: Correct all admin quiz operations**

Document all eight admin quiz operations:

```text
GET, POST /api/v1/admin/courses/{course_uuid}/quiz
PATCH, DELETE /api/v1/admin/quizzes/{quiz_uuid}
GET, POST /api/v1/admin/quizzes/{quiz_uuid}/questions
PATCH, DELETE /api/v1/admin/quiz-questions/{question_uuid}
```

Build request schemas directly from `QuizzStoreRequest`, `QuizzUpdateRequest`, `QuestionBatchStoreRequest`, and `QuestionUpdateRequest`. Include nested options, correctness flags, points, position, and image URL rules. Admin responses may use `AnswerResource.is_correct`; student responses may not.

- [ ] **Step 4: Add all admin student operations**

Define:

- list queries: `search`, `email_verified`, allowed `sort_by`, `sort_direction`, `limit`, and `page`;
- list response from `StudentListResource` plus pagination metadata;
- detail response from `StudentDetailResource`, including role and `learning_summary`;
- progress response from `StudentProgressCourseResource`;
- certificates response from Student feature `StudentCertificateResource` plus pagination metadata.

All operations document `401`, `403`, `404` where applicable, query validation `422`, and handled `500` responses.

- [ ] **Step 5: Correct admin certificate operations**

List pagination accepts only `limit` and `page`. Revoke requires `reason` as a string of at most 1000 characters. Responses match `CertificateResource`, including nullable revocation fields and nested student/course data.

- [ ] **Step 6: Commit admin contracts**

```powershell
git add openapi.yaml
git commit -m "docs: synchronize admin API contracts"
```

### Task 4: Normalize reusable components and examples

**Files:**
- Modify: `openapi.yaml`

- [ ] **Step 1: Normalize common scalar and envelope schemas**

Ensure reusable definitions include:

```text
UUID (string, uuid)
ISO date and date-time nullable variants compatible with the selected OpenAPI version
PaginationMeta with current_page, per_page, total, last_page
MessageResponse
ErrorResponse with success=false and message
ValidationErrorResponse with message and errors: map<string, string[]>
```

Do not force `success` onto backend responses that omit it, notably invalid login, logout success, and `/auth/me`.

- [ ] **Step 2: Normalize security declarations**

Represent the implemented Laravel Sanctum session flow with cookie-based authentication and CSRF requirements in descriptions. Apply security only to routes protected by `auth:sanctum`; keep login, Google redirects, public certificate API, and HTML verification public.

- [ ] **Step 3: Normalize common responses without overclaiming**

Reuse response components only when schema shape is identical. Keep operation-specific descriptions/messages and include:

```text
401 unauthenticated
403 role/domain authorization failure
404 missing entity
422 validation or domain rejection
429 rate limited
500 handled server/domain operation failure
```

- [ ] **Step 4: Add complete representative examples**

Every request body and success response receives a complete example. Error components receive representative validation and authorization examples. UUIDs use valid UUID strings; timestamps use RFC 3339; nullable fields are shown explicitly when useful.

- [ ] **Step 5: Commit component cleanup**

```powershell
git add openapi.yaml
git commit -m "docs: normalize OpenAPI schemas and responses"
```

### Task 5: Validate structure, references, and backend parity

**Files:**
- Verify: `openapi.yaml`

- [ ] **Step 1: Parse YAML and detect duplicate-key loss**

Run:

```powershell
python -c "import yaml; d=yaml.safe_load(open('openapi.yaml', encoding='utf-8')); assert isinstance(d, dict); print(d['openapi'], len(d['paths']))"
```

Expected: a valid OpenAPI version and the final path count.

- [ ] **Step 2: Resolve all local references**

Run a Python check that recursively collects every `$ref` beginning with `#/`, traverses the loaded document by JSON Pointer segments, and fails if a target is absent.

Expected: `0 unresolved refs`.

- [ ] **Step 3: Compare operations with Laravel routes**

Normalize Laravel `GET|HEAD` to `get`, prepend `/` to route URIs, and compare with OpenAPI methods under `/api/v1/**`.

Expected:

```text
missing_in_openapi=[]
extra_in_openapi=[]
```

- [ ] **Step 4: Run semantic OpenAPI linting**

Run:

```powershell
npx --yes @redocly/cli lint openapi.yaml
```

Expected: no structural OpenAPI errors. Style warnings may only remain when they conflict with exact backend names or response behavior; document any such warning in the handoff.

- [ ] **Step 5: Run repository whitespace checks**

```powershell
git diff --check
git status --short
```

Expected: no whitespace errors; only intended OpenAPI/plan changes are present alongside pre-existing user-owned frontend changes.

- [ ] **Step 6: Commit final validation adjustments**

```powershell
git add openapi.yaml
git commit -m "docs: validate synchronized OpenAPI specification"
```

