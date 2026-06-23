# Backend Development Rules - HISSA

Dokumen ini adalah aturan wajib bagi AI agent ketika membuat, mengubah, atau memperbaiki fitur backend HISSA.

Tujuan dokumen ini adalah memastikan setiap implementasi backend memiliki struktur, alur dependency, validasi, response API, dan test yang konsisten.

Backend HISSA menggunakan struktur feature-based di:

```text
backend/app/Features
```

Kata kunci normatif yang digunakan:

- **MUST**: wajib dipatuhi.
- **MUST NOT**: dilarang.
- **SHOULD**: sangat disarankan; penyimpangan harus memiliki alasan teknis.
- **MAY**: opsional.

---

## 1. Prinsip Utama

1. Agent **MUST** membaca struktur fitur yang sudah ada sebelum membuat fitur baru.
2. Agent **MUST** mengikuti struktur folder di `backend/app/Features/<Feature>`.
3. Agent **MUST** menjaga boundary antar layer: Controller, Request, Resource, Service, Contract, Repository, DTO, Model, dan Exception.
4. Agent **MUST** menggunakan Eloquent ORM untuk interaksi database di layer repository.
5. Agent **MUST NOT** menaruh query database langsung di controller.
6. Agent **MUST NOT** menaruh business rule kompleks di controller atau request validation.
7. Agent **MUST** membuat request validation untuk setiap endpoint yang menerima input.
8. Agent **MUST** membuat resource response untuk data yang dikembalikan endpoint.
9. Agent **MUST** membuat unit test atau feature test sesuai aturan di `unit-test-rules.md`.
10. Agent **MUST** menerapkan clean code: nama jelas, fungsi fokus, dependency eksplisit, dan tidak ada duplikasi yang tidak perlu.

---

## 2. Struktur Folder Fitur

Setiap fitur backend **MUST** berada di:

```text
backend/app/Features/<Feature>
```

Struktur fitur **SHOULD** mengikuti pola berikut:

```text
backend/app/Features/<Feature>/
|-- Contracts/
|-- DTOs/
|-- Exceptions/
|-- Http/
|   |-- Controllers/
|   |-- Requests/
|   `-- Resources/
|-- Models/
|-- Repositories/
|-- Rules/
`-- Services/
```

Catatan:

1. Folder contract yang benar adalah `Contracts`, bukan `Contact`.
2. Folder yang belum dibutuhkan **MAY** tidak dibuat, tetapi setiap layer yang digunakan **MUST** berada di folder standar tersebut.
3. Namespace **MUST** mengikuti path folder.

Contoh namespace:

```php
namespace App\Features\Course\Contracts;
namespace App\Features\Course\Repositories;
namespace App\Features\Course\Http\Controllers;
```

---

## 3. Alur Wajib Pembuatan Fitur API

Setiap pembuatan fitur atau layanan API **MUST** mengikuti urutan berikut:

1. Buat interface atau abstraksi di `Contracts`.
2. Implementasikan interface di `Repositories` dengan Eloquent ORM.
3. Buat DTO di `DTOs` sebagai data transfer object antar layer.
4. Buat custom exception di folder fitur atau `backend/app/GlobalExceptions`.
5. Buat service di `Services` untuk business logic.
6. Buat request validation di `Http/Requests`.
7. Buat resource response di `Http/Resources`.
8. Buat controller di `Http/Controllers`.
9. Buat provider binding di `backend/app/Providers/<Feature>/<Feature>ServiceProvider.php`.
10. Daftarkan route di `backend/routes/api/v1/<feature>.php`.
11. Buat test sesuai `unit-test-rules.md`.
12. Jalankan verifikasi test yang relevan.

Agent **MUST NOT** melewati layer hanya karena fitur terlihat sederhana, kecuali perubahan benar-benar tidak membuat endpoint, tidak menyentuh database, dan tidak menambah business behavior.

---

## 4. Contracts

Contract **MUST** menjadi abstraksi dependency untuk repository atau service yang perlu di-inject.

Lokasi:

```text
backend/app/Features/<Feature>/Contracts
```

Aturan:

1. Interface **MUST** memiliki nama eksplisit, misalnya `CourseRepositoryContract`.
2. Method contract **MUST** mendeskripsikan behavior, bukan detail implementasi database.
3. Return type **MUST** eksplisit.
4. Nullable return **MUST** dinyatakan jelas dengan `?Type` jika data boleh tidak ditemukan.
5. Contract dan implementasi **MUST** konsisten terhadap exception, nullable return, pagination, dan collection.

Contoh:

```php
interface CourseRepositoryContract
{
    public function findByUuid(string $courseUuid): ?Course;
}
```

---

## 5. Repositories

Repository **MUST** menjadi satu-satunya layer utama yang berinteraksi langsung dengan database untuk fitur tersebut.

Lokasi:

```text
backend/app/Features/<Feature>/Repositories
```

Aturan:

1. Repository **MUST** mengimplementasikan contract yang sesuai.
2. Repository **MUST** menggunakan Eloquent ORM.
3. Repository **MUST NOT** membaca request HTTP.
4. Repository **MUST NOT** mengembalikan response JSON.
5. Repository **MUST** fokus pada query, persistence, dan mapping data database.
6. Query yang berulang **SHOULD** dipindahkan ke method repository yang jelas.
7. Repository **MUST** menghormati contract terkait return `null`, exception, collection, atau paginator.

Contoh nama:

```text
EloquentCourseRepository.php
EloquentUserRepository.php
```

---

## 6. DTOs

DTO **MUST** digunakan sebagai data transfer object antar layer agar struktur data konsisten.

Lokasi:

```text
backend/app/Features/<Feature>/DTOs
```

Aturan:

1. DTO **MUST** memiliki property typed.
2. DTO **SHOULD** dibuat immutable dengan `readonly` jika datanya tidak perlu berubah.
3. DTO **MUST** dibuat dari data yang sudah divalidasi, bukan dari raw request yang belum tervalidasi.
4. Request object **SHOULD** menyediakan method seperti `toDTO()` bila endpoint membutuhkan DTO.
5. Service **SHOULD** menerima DTO untuk input yang memiliki struktur stabil.
6. DTO **MUST NOT** berisi query database.
7. DTO **MUST NOT** mengembalikan response JSON.

Contoh:

```php
final readonly class CourseListData
{
    public function __construct(
        public ?string $search,
        public int $perPage,
    ) {
    }
}
```

---

## 7. Exceptions

Exception **MUST** ditempatkan sesuai cakupan penggunaannya.

Exception khusus fitur:

```text
backend/app/Features/<Feature>/Exceptions
```

Exception global:

```text
backend/app/GlobalExceptions
```

Aturan:

1. Exception yang hanya berlaku untuk satu fitur **MUST** dibuat di `Features/<Feature>/Exceptions`.
2. Exception yang digunakan banyak fitur **MUST** dibuat di `GlobalExceptions`.
3. Exception **MUST** memiliki nama domain yang jelas, misalnya `CourseOperationException` atau `AuthorizationException`.
4. Service **MAY** melempar domain exception untuk business failure.
5. Controller **MUST** menangkap exception yang memang bisa dipulihkan menjadi response API.
6. Agent **MUST NOT** menangkap `Throwable` secara luas kecuali ada alasan teknis yang kuat.
7. Error internal **SHOULD** dilaporkan dengan `report($exception)` sebelum response server error.

---

## 8. Services

Service **MUST** menjadi tempat business logic fitur.

Lokasi:

```text
backend/app/Features/<Feature>/Services
```

Aturan:

1. Service **MUST** menerima dependency melalui constructor injection.
2. Service **SHOULD** bergantung pada contract, bukan implementasi concrete repository.
3. Service **MUST NOT** membaca raw request HTTP.
4. Service **MUST NOT** mengembalikan response JSON.
5. Service **MUST** menerima DTO, scalar, model authenticated user, atau object domain yang jelas.
6. Authorization sederhana yang sangat dekat dengan business rule **MAY** berada di service.
7. Logic yang dipakai banyak fitur **SHOULD** diekstrak ke service global atau class reusable yang jelas.

---

## 9. HTTP Requests

Setiap endpoint yang menerima input **MUST** memiliki request validation.

Lokasi:

```text
backend/app/Features/<Feature>/Http/Requests
```

Aturan:

1. Request class **MUST** berisi validation rules.
2. Controller **MUST** menggunakan request class, bukan `Request` generic, untuk endpoint dengan input.
3. Request **SHOULD** memiliki method `toDTO()` bila service membutuhkan DTO.
4. Request **MUST NOT** berisi query database kompleks.
5. Request **MUST NOT** menjalankan business action.
6. Custom validation rule fitur **MAY** ditempatkan di `Features/<Feature>/Rules`.

Contoh:

```php
public function toDTO(): CourseListData
{
    $validated = $this->validated();

    return new CourseListData(
        search: $validated['search'] ?? null,
        perPage: (int) ($validated['per_page'] ?? 15),
    );
}
```

---

## 10. HTTP Resources

Setiap endpoint yang mengembalikan data model atau collection **MUST** menggunakan resource.

Lokasi:

```text
backend/app/Features/<Feature>/Http/Resources
```

Aturan:

1. Resource **MUST** menentukan field response secara eksplisit.
2. Resource **MUST NOT** mengekspos data sensitif seperti password, token internal, atau remember token.
3. Resource **SHOULD** menggunakan nama field yang stabil untuk kontrak frontend.
4. Resource **MAY** menggunakan conditional field dengan `when()` jika data hanya muncul pada kondisi tertentu.
5. Controller **MUST** membungkus model dengan resource sebelum dikirim sebagai response.

Contoh:

```php
'data' => new CourseResource($course)
```

---

## 11. Controllers

Controller **MUST** menjadi orchestration layer untuk request dan response.

Lokasi:

```text
backend/app/Features/<Feature>/Http/Controllers
```

Aturan:

1. Controller **MUST** menerima request validation class jika endpoint memiliki input.
2. Controller **MUST** memanggil service untuk business operation.
3. Controller **MUST NOT** menjalankan query Eloquent langsung.
4. Controller **MUST NOT** menyimpan business rule kompleks.
5. Controller **MUST** mengembalikan JSON response dengan struktur standar.
6. Controller **MUST** menggunakan resource untuk field `data`.
7. Controller **SHOULD** menggunakan status code yang tepat: `200`, `201`, `204`, `400`, `401`, `403`, `404`, `422`, atau `500`.

Struktur response sukses **MUST**:

```php
return response()->json([
    'success' => true,
    'message' => 'Pesan berhasil.',
    'data' => $data,
]);
```

Struktur response error **SHOULD**:

```php
return response()->json([
    'success' => false,
    'message' => 'Pesan error.',
], 404);
```

Untuk response pagination, controller **MAY** menambahkan `meta`:

```php
return response()->json([
    'success' => true,
    'message' => 'Daftar data berhasil diambil.',
    'data' => FeatureResource::collection($items),
    'meta' => [
        'current_page' => $items->currentPage(),
        'per_page' => $items->perPage(),
        'total' => $items->total(),
        'last_page' => $items->lastPage(),
    ],
]);
```

---

## 12. Provider Binding

Setiap fitur **MUST** memiliki provider binding sendiri.

Lokasi:

```text
backend/app/Providers/<Feature>/<Feature>ServiceProvider.php
```

Contoh:

```text
backend/app/Providers/User/UserServiceProvider.php
backend/app/Providers/Course/CourseServiceProvider.php
```

Aturan:

1. Provider fitur **MUST** mendaftarkan binding contract ke implementasi concrete.
2. Binding repository **MUST** dilakukan di method `register()`.
3. Provider **MUST** berada pada namespace `App\Providers\<Feature>`.
4. Provider baru **MUST** didaftarkan ke konfigurasi provider aplikasi sesuai pola Laravel yang dipakai project.
5. Agent **MUST** memverifikasi provider sudah dimuat sebelum menganggap dependency injection selesai.

Contoh:

```php
public function register(): void
{
    $this->app->bind(CourseRepositoryContract::class, EloquentCourseRepository::class);
}
```

---

## 13. Routes

Setiap fitur API **MUST** memiliki file route sendiri di:

```text
backend/routes/api/v1/<feature>.php
```

Contoh:

```text
backend/routes/api/v1/courses.php
backend/routes/api/v1/auth.php
```

Aturan:

1. Route fitur **MUST** didaftarkan melalui file route fitur sendiri.
2. File route fitur **MUST** di-include dari `backend/routes/api/v1/api.php`.
3. Route **MUST** memakai controller dari fitur terkait.
4. Route **SHOULD** memiliki prefix dan route name yang konsisten.
5. Route **SHOULD** memakai middleware yang eksplisit seperti `auth:sanctum`, `throttle:api`, atau authorization middleware bila dibutuhkan.
6. Parameter route **SHOULD** menggunakan nama yang jelas, misalnya `{course_uuid}`.

Contoh:

```php
Route::prefix('admin/courses')
    ->name('admin.courses.')
    ->middleware('throttle:api')
    ->group(function (): void {
        Route::get('/', [CourseController::class, 'index'])->name('index');
    });
```

---

## 14. Model dan Database

Model fitur **MUST** berada di:

```text
backend/app/Features/<Feature>/Models
```

Aturan:

1. Model **MUST** memiliki namespace sesuai fitur.
2. Model **SHOULD** mendefinisikan `fillable`, `casts`, relation, dan hidden attribute dengan jelas.
3. Model **MUST** memakai factory yang sesuai bila akan digunakan dalam test.
4. Migration **MUST** kompatibel dengan environment test SQLite in-memory jika fitur diuji dengan database.
5. Agent **MUST NOT** menaruh business flow panjang di model.

---

## 15. Testing

Setiap fitur baru atau perubahan behavior **MUST** memiliki test sesuai `unit-test-rules.md`.

Aturan:

1. Agent **MUST** membaca `unit-test-rules.md` sebelum membuat atau mengubah test.
2. Unit test fitur **MUST** berada di `backend/tests/Unit/Features/<Feature>`.
3. Feature test endpoint **SHOULD** berada di `backend/tests/Feature/<Feature>`.
4. Controller dan route **MUST** diuji melalui HTTP feature test, bukan memanggil method controller langsung.
5. DTO, enum, service, repository, dan model **SHOULD** diuji sesuai layer dan risiko.
6. Test **MUST** menggunakan Pest style.
7. Test database **MUST** memakai `RefreshDatabase`.
8. Agent **MUST** menjalankan test relevan setelah membuat atau mengubah fitur.
9. Agent **MUST NOT** mengklaim test berhasil tanpa menjalankan command.

Command minimal dari folder `backend`:

```bash
php artisan test
```

Untuk perubahan spesifik:

```bash
php artisan test tests/Feature/<Feature>/<UseCase>Test.php
php artisan test tests/Unit/Features/<Feature>/<Layer>/<ClassName>Test.php
```

---

## 16. Clean Code

Agent **MUST** menerapkan clean code pada setiap perubahan.

Aturan:

1. Class **MUST** memiliki satu tanggung jawab utama.
2. Method **SHOULD** pendek dan fokus pada satu behavior.
3. Nama class, method, variable, dan DTO property **MUST** jelas.
4. Dependency **MUST** eksplisit melalui constructor atau method injection.
5. Agent **MUST NOT** membuat helper global untuk kebutuhan satu fitur.
6. Agent **MUST NOT** menduplikasi logic validasi, mapping, atau query tanpa alasan.
7. Agent **MUST** menghapus dead code, import tidak terpakai, dan debug statement.
8. Agent **MUST NOT** meninggalkan `dd()`, `dump()`, `var_dump()`, `ray()`, atau komentar TODO tanpa konteks.
9. Agent **SHOULD** memakai `final` untuk class yang tidak didesain untuk inheritance, mengikuti pola yang sudah ada.
10. Agent **SHOULD** menjaga style code sesuai standar Laravel dan pola project.

---

## 17. Security dan Data Safety

1. Endpoint protected **MUST** memakai middleware auth atau authorization yang sesuai.
2. Response **MUST NOT** mengekspos field sensitif.
3. Input **MUST** divalidasi melalui request validation.
4. Agent **MUST NOT** mempercayai input dari request tanpa validasi.
5. File upload, token, password, ownership resource, dan OAuth **MUST** mendapat test keamanan bila disentuh.
6. Error response **SHOULD NOT** membocorkan stack trace, query SQL, token, credential, atau detail internal.

---

## 18. Definition of Done

Fitur backend dianggap selesai hanya jika:

- [ ] Struktur folder mengikuti `backend/app/Features/<Feature>`.
- [ ] Contract dibuat di `Contracts`.
- [ ] Repository mengimplementasikan contract dan memakai Eloquent ORM.
- [ ] DTO dibuat untuk transfer data antar layer yang membutuhkan struktur stabil.
- [ ] Exception ditempatkan di fitur atau `GlobalExceptions` sesuai cakupan.
- [ ] Service berisi business logic.
- [ ] Request validation dibuat di `Http/Requests`.
- [ ] Resource response dibuat di `Http/Resources`.
- [ ] Controller mengembalikan JSON dengan `success`, `message`, dan `data` untuk response sukses.
- [ ] Provider binding dibuat di `backend/app/Providers/<Feature>`.
- [ ] Route fitur dibuat di `backend/routes/api/v1/<feature>.php`.
- [ ] File route fitur di-include dari `backend/routes/api/v1/api.php`.
- [ ] Unit test atau feature test dibuat sesuai `unit-test-rules.md`.
- [ ] Test relevan sudah dijalankan dan hasilnya dilaporkan.
- [ ] Tidak ada debug statement, dead code, atau import tidak terpakai.
- [ ] Clean code diterapkan dan perubahan tetap sesuai pola project.

---

## 19. Format Laporan Agent Setelah Implementasi

Setelah membuat atau mengubah fitur backend, agent **SHOULD** melaporkan:

```text
Ringkasan:
- Menambahkan fitur Course dengan contract, repository, DTO, service, request, resource, controller, provider, dan route.

File:
- backend/app/Features/Course/Contracts/CourseRepositoryContract.php
- backend/app/Features/Course/Repositories/EloquentCourseRepository.php
- backend/routes/api/v1/courses.php

Validasi:
- php artisan test tests/Feature/Course/CourseIndexTest.php: berhasil
- php artisan test --testsuite=Unit: berhasil

Catatan:
- Repository memakai Eloquent ORM.
- Response API mengikuti struktur success, message, dan data.
```

Jika validasi gagal, agent **MUST** melaporkan command, error utama, dan status perbaikannya.
