# OpenAPI–Backend Synchronization Design

## Objective

Synchronize `openapi.yaml` with the routes and observable HTTP contracts implemented by the Laravel backend. The resulting document must describe complete request parameters, request bodies, success responses, error responses, authentication requirements, and non-JSON responses.

## Source of Truth

The backend implementation is authoritative, in this order:

1. Laravel route definitions and `artisan route:list` determine paths, HTTP methods, middleware, and controller actions.
2. Form Request classes determine query and body validation rules.
3. Controllers determine status codes, response envelopes, and error responses.
4. API Resource classes determine serialized response fields and types.
5. Models, migrations, services, and tests resolve types or conditional behavior not explicit in the HTTP layer.

Existing OpenAPI content may be reused only after it is verified against those sources.

## Scope

The document will cover every backend API route under `/api/v1/**`, including:

- authentication and Google OAuth;
- student courses, lessons, progress, quizzes, attempts, and certificates;
- public certificate verification API;
- admin courses, lessons, lesson videos, quizzes, questions, students, and certificates.

The browser route `/verify/{certificate_number}` remains documented. It is represented as an HTML response, while certificate downloads are represented as binary PDF responses.

No backend or frontend behavior will be changed.

## OpenAPI Structure

`openapi.yaml` remains a single OpenAPI 3.x document. Paths and operation methods will exactly match the backend. Repeated elements will be represented under `components`, including:

- bearer/cookie authentication definitions as required by the implemented Sanctum flow;
- UUID and certificate-number path parameters;
- pagination metadata;
- standard success and error envelopes;
- validation, unauthenticated, forbidden, not-found, conflict, throttling, and server-error responses;
- domain schemas for courses, lessons, videos, progress, quizzes, questions, answers, attempts, students, and certificates.

Operation-specific schemas will be used when the same domain object has different list/detail or admin/student projections. This avoids claiming fields that a Resource does not serialize.

## Request Contracts

Each operation will document all implemented inputs:

- path parameters with correct names and formats;
- query parameters, defaults, allowed values, and pagination constraints;
- JSON request bodies with required and optional fields derived from Form Request rules;
- nested arrays and conditional validation rules for quiz questions and submitted answers;
- nullable fields and partial-update semantics for `PATCH` operations.

Operations without a request body will not declare one.

## Response Contracts

Each operation will document its actual success status and media type. JSON response schemas will include the controller envelope (`success`, `message`, `data`, and `meta` where present) and the exact Resource fields. Empty delete responses, redirects, streamed PDFs, and HTML pages will use their real status/media semantics.

Documented errors will be limited to responses that can arise from route middleware, Laravel validation, controller branches, or handled domain exceptions. Examples will use representative values and Indonesian messages consistent with the backend without making message text a rigid client contract.

## Known Corrections

The synchronization includes at least these confirmed corrections:

- Google OAuth paths move under `/api/v1/auth/google/**`.
- Final-quiz creation uses `POST`, not `PUT`.
- Missing admin quiz read/update/delete operations are added.
- Missing admin student list, detail, progress, and certificate operations are added.
- Existing schemas, status codes, parameter names, and response envelopes are rechecked rather than assumed correct.

## Verification

Completion requires:

1. parsing `openapi.yaml` successfully as YAML;
2. validating it as an OpenAPI document with an available local validator;
3. comparing every documented API path and method against `php artisan route:list --path=api --json`;
4. confirming all local `$ref` targets resolve;
5. manually checking request and response schemas against Form Requests, Controllers, and Resources.

