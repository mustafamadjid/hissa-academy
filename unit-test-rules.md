# Backend Unit Test Rules - HISSA

Dokumen ini adalah aturan wajib bagi AI agent ketika membuat, mengubah, atau memperbaiki unit test pada backend HISSA.

Backend HISSA menggunakan:

- Laravel 13
- PHP 8.3
- Pest 4
- PHPUnit
- Laravel Sanctum
- Laravel Socialite
- SQLite in-memory untuk environment testing
- Struktur feature-based di `backend/app/Features`

Kata kunci normatif yang digunakan:

- **MUST**: wajib dipatuhi.
- **MUST NOT**: dilarang.
- **SHOULD**: sangat disarankan; penyimpangan harus memiliki alasan teknis.
- **MAY**: opsional.

---

## 1. Prinsip Utama

1. Agent **MUST** membaca implementasi yang akan diuji sebelum menulis test.
2. Agent **MUST** mengikuti struktur backend yang sudah ada, terutama `app/Features/<Feature>`.
3. Agent **MUST** menggunakan Pest sebagai gaya utama test.
4. Agent **MUST NOT** menambahkan framework testing baru.
5. Agent **MUST** membuat test yang memverifikasi behavior, bukan detail implementasi internal yang rapuh.
6. Agent **MUST** memilih jenis test yang paling kecil tetapi cukup untuk membuktikan behavior.
7. Agent **MUST NOT** melakukan request HTTP nyata ke service eksternal.
8. Agent **MUST** membuat test deterministik: tidak bergantung pada urutan test, waktu nyata, network, atau data lokal developer.
9. Agent **MUST** menjaga test mudah dibaca oleh agent lain: Arrange, Act, Assert harus jelas.
10. Agent **MUST** menjalankan test yang relevan setelah membuat atau mengubah test.

---

## 2. Struktur Backend yang Harus Dipahami

Source backend berada di:

```text
backend/
|-- app/
|   |-- Features/
|   |   |-- Auth/
|   |   |-- Certificate/
|   |   |-- Course/
|   |   |-- Lesson/
|   |   |-- LessonVideo/
|   |   |-- Quizz/
|   |   |-- User/
|   |   `-- UserProgress/
|   `-- Providers/
|-- database/
|   |-- factories/
|   |   `-- Features/
|   |-- migrations/
|   `-- seeders/
|-- routes/
|   `-- api/
|       `-- v1/
|-- tests/
|   |-- Feature/
|   |-- Unit/
|   |   `-- Features/
|   |       |-- Auth/
|   |       |-- Certificate/
|   |       |-- Course/
|   |       |-- Lesson/
|   |       |-- LessonVideo/
|   |       |-- Quizz/
|   |       |-- User/
|   |       `-- UserProgress/
|   |-- Pest.php
|   `-- TestCase.php
|-- composer.json
`-- phpunit.xml
```

Namespace utama:

```text
App\Features\<Feature>\...
Database\Factories\Features\<Feature>\Models\...
Tests\...
```

Agent **MUST** menyesuaikan test dengan namespace dan lokasi class yang ada.

---

## 3. Perbedaan Unit Test dan Feature Test

### 3.1 Unit test

Unit test digunakan untuk menguji satu unit kecil secara terisolasi.

Contoh target unit test:

- DTO.
- Enum.
- Service dengan dependency yang di-mock.
- Repository contract behavior bila tidak membutuhkan full HTTP flow.
- Pure helper atau class kecil bila nanti ada.
- Method model yang tidak membutuhkan full request lifecycle.

Unit test **MUST** ditempatkan di subfolder fitur masing-masing:

```text
backend/tests/Unit/Features/<Feature>/<Layer>/<ClassName>Test.php
```

Setiap fitur backend **MUST** memiliki subfolder unit test sendiri di bawah `backend/tests/Unit/Features`, walaupun belum semua layer pada fitur tersebut memiliki test.

Struktur subfolder fitur unit test yang wajib dipertahankan:

```text
backend/tests/Unit/Features/
|-- Auth/
|-- Certificate/
|-- Course/
|-- Lesson/
|-- LessonVideo/
|-- Quizz/
|-- User/
`-- UserProgress/
```

Agent **MUST NOT** menaruh unit test langsung di `backend/tests/Unit` jika test tersebut milik salah satu fitur. `backend/tests/Unit` hanya boleh berisi bootstrap, test umum yang benar-benar lintas fitur, atau file legacy yang sedang menunggu dipindahkan.

Jika subfolder fitur belum memiliki test, folder tersebut **MAY** berisi `.gitkeep` agar struktur tetap tersimpan di Git.

Contoh:

```text
backend/tests/Unit/Features/Auth/DTOs/LoginDataTest.php
backend/tests/Unit/Features/Auth/Services/UserAuthServiceTest.php
backend/tests/Unit/Features/User/Enums/UserRoleTest.php
backend/tests/Unit/Features/Course/Models/CourseTest.php
```

### 3.2 Feature test

Feature test digunakan untuk menguji behavior yang membutuhkan Laravel request lifecycle, routing, middleware, validation, session, auth guard, database integration, atau response HTTP.

Contoh target feature test:

- Controller endpoint.
- Route registration.
- Form request validation.
- Auth flow.
- Middleware behavior.
- JSON response contract.
- Database persistence melalui endpoint.

Feature test **SHOULD** ditempatkan di:

```text
backend/tests/Feature/<Feature>/<UseCase>Test.php
```

Contoh:

```text
backend/tests/Feature/Auth/UserLoginTest.php
backend/tests/Feature/Auth/GoogleAuthCallbackTest.php
backend/tests/Feature/Course/CourseIndexTest.php
backend/tests/Feature/UserProgress/UserProgressStoreTest.php
```

### 3.3 Aturan pemilihan

1. Jika test memanggil endpoint seperti `$this->postJson()`, gunakan **Feature test**.
2. Jika test membutuhkan middleware, session, cookie, guard, atau route, gunakan **Feature test**.
3. Jika test hanya memanggil method class secara langsung, gunakan **Unit test**.
4. Jika test adalah unit test untuk fitur tertentu, file **MUST** berada di `tests/Unit/Features/<Feature>`.
5. Jika unit membutuhkan database Eloquent untuk membuktikan query atau relation, unit test **MAY** menggunakan database, tetapi harus memakai `RefreshDatabase`.
6. Jika mocking membuat test lebih kompleks daripada menjalankan flow Laravel, gunakan Feature test yang lebih realistis.

---

## 4. Konfigurasi Testing Proyek

`backend/phpunit.xml` sudah mengatur:

```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
<env name="MAIL_MAILER" value="array"/>
<env name="BCRYPT_ROUNDS" value="4"/>
```

Implikasi wajib:

1. Agent **MUST NOT** mengubah `.env` untuk menjalankan test.
2. Agent **MUST NOT** bergantung pada database lokal seperti MySQL developer.
3. Agent **MUST** mengasumsikan test berjalan di SQLite in-memory.
4. Agent **MUST** memastikan migration kompatibel dengan SQLite bila test menggunakan database.
5. Agent **MUST NOT** menulis test yang membutuhkan queue async nyata, mail server nyata, atau cache eksternal.

---

## 5. Pest Style yang Wajib Dipakai

Gunakan Pest function style:

```php
<?php

use App\Features\Auth\DTOs\LoginData;

it('creates login data from validated payload', function () {
    $data = new LoginData(
        email: 'student@example.com',
        password: 'password',
    );

    expect($data->email)->toBe('student@example.com')
        ->and($data->password)->toBe('password');
});
```

Aturan:

1. File test **MUST** diawali `<?php`.
2. Test name **SHOULD** menjelaskan behavior, bukan nama method saja.
3. Test name **SHOULD** menggunakan bahasa Inggris agar konsisten dengan test yang sudah ada.
4. Satu test **SHOULD** memverifikasi satu behavior utama.
5. Gunakan `it('...')` untuk behavior dan `test('...')` bila lebih natural.
6. Grouping dengan `describe()` **MAY** digunakan bila file memiliki banyak scenario.
7. Agent **MUST NOT** membuat assertion kosong atau test yang hanya mengecek `true`.
8. Agent **MUST NOT** meninggalkan `.only`, `.skip`, `dd()`, `dump()`, atau debug output.

---

## 6. Struktur Arrange, Act, Assert

Setiap test **MUST** mudah dipetakan ke tiga bagian:

```php
it('updates the last login timestamp after successful login', function () {
    // Arrange
    $user = User::factory()->create([
        'email' => 'student@example.com',
        'password' => Hash::make('password'),
    ]);

    // Act
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'student@example.com',
        'password' => 'password',
    ]);

    // Assert
    $response->assertOk();
    expect($user->fresh()->last_login_at)->not->toBeNull();
});
```

Komentar `Arrange`, `Act`, `Assert` **MAY** dipakai ketika test cukup panjang. Untuk test pendek, struktur dapat terlihat dari blank line.

---

## 7. Setup Laravel TestCase

Saat ini `backend/tests/Pest.php` mengikat `Tests\TestCase` hanya untuk folder `Feature`:

```php
pest()->extend(TestCase::class)
    ->in('Feature');
```

Implikasi:

1. Test di `tests/Feature` dapat memakai `$this->getJson()`, `$this->postJson()`, `actingAs()`, dan helper Laravel testing lain.
2. Test di `tests/Unit` secara default tidak memakai Laravel `TestCase`.
3. Unit test murni **SHOULD** tidak membutuhkan `$this`.
4. Jika unit test membutuhkan container Laravel, facade, helper app, atau database, agent **MUST** menambahkan `uses(Tests\TestCase::class);` pada file tersebut atau memindahkan test ke `tests/Feature` bila behavior-nya memang integration-level.

Contoh unit test yang membutuhkan Laravel:

```php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('finds a user by email', function () {
    // ...
});
```

---

## 8. Database dan RefreshDatabase

Test yang menyentuh database **MUST** memakai `RefreshDatabase`.

Untuk Feature test:

```php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
```

Untuk Unit test yang memakai Laravel TestCase:

```php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
```

Aturan:

1. Agent **MUST** memakai factory untuk membuat model bila factory tersedia.
2. Agent **MUST NOT** bergantung pada seeder global kecuali behavior yang diuji memang seeder.
3. Agent **MUST** membuat data minimal yang dibutuhkan test.
4. Agent **MUST** menggunakan `assertDatabaseHas`, `assertDatabaseMissing`, atau `assertSoftDeleted` untuk persistence penting.
5. Agent **MUST NOT** membuat test yang bergantung pada ID tertentu kecuali ID dibuat eksplisit dalam test.
6. Agent **MUST** memperhatikan soft delete pada model yang memakai `SoftDeletes`.
7. Agent **MUST** memastikan role atau relasi wajib dibuat sebelum factory user digunakan bila migration membutuhkan foreign key.

Catatan proyek:

- `User` memakai `SoftDeletes`.
- `Course` memiliki factory.
- Factory berada di `database/factories/Features/<Feature>/Models`.
- `UserFactory` saat ini membuat `role_id` dengan `Role::query()->firstOrCreate(['name' => 'user'])`.

---

## 9. Factory Rules

1. Agent **MUST** menggunakan factory model yang sudah ada sebelum membuat data manual.
2. Agent **MUST** menambahkan factory baru bila model sering dibutuhkan test dan factory belum ada.
3. Factory baru **MUST** mengikuti lokasi proyek:

```text
backend/database/factories/Features/<Feature>/Models/<ModelName>Factory.php
```

4. Factory baru **MUST** memiliki namespace:

```php
namespace Database\Factories\Features\<Feature>\Models;
```

5. Model **MUST** menggunakan `HasFactory` dan mengarah ke factory yang benar.
6. Factory **MUST** mengisi field wajib sesuai migration.
7. Factory **SHOULD** memiliki state method untuk scenario umum.

Contoh state:

```php
public function verified(): static
{
    return $this->state(fn () => [
        'email_verified_at' => now(),
    ]);
}
```

---

## 10. Mocking Rules

Gunakan mocking hanya untuk dependency eksternal atau boundary yang jelas.

Dependency yang boleh atau harus di-mock:

- Repository contract ketika menguji service.
- Socialite ketika menguji Google auth.
- HTTP client eksternal bila nanti ada.
- Queue, notification, mail, event, storage, atau log bila behavior utama adalah dispatching.

Dependency yang tidak perlu di-mock:

- DTO sederhana.
- Enum.
- Value object.
- Eloquent relation sederhana.
- Laravel collection transformation sederhana.

Contoh mock contract:

```php
use App\Features\User\Contracts\UserRepositoryContract;
use Mockery;

$repository = Mockery::mock(UserRepositoryContract::class);
$repository->shouldReceive('findByEmail')
    ->once()
    ->with('student@example.com')
    ->andReturn($user);
```

Aturan:

1. Agent **MUST NOT** mock class yang sedang diuji.
2. Agent **MUST NOT** over-mock sampai test hanya menguji mock setup.
3. Agent **MUST** memverifikasi interaction penting dengan `once()`, `never()`, atau argument expectation.
4. Agent **SHOULD** lebih memilih fake Laravel (`Mail::fake()`, `Notification::fake()`, `Event::fake()`, `Storage::fake()`, `Queue::fake()`) ketika tersedia.
5. Agent **MUST** membersihkan atau menghindari state global mock antar-test.

---

## 11. Auth Testing Rules

Backend memiliki auth flow berbasis:

- Session/web guard untuk login biasa dan Google OAuth.
- Sanctum untuk endpoint `/api/v1/auth/me`.

Aturan:

1. Endpoint login **MUST** diuji dengan request JSON ke `/api/v1/auth/login`.
2. Endpoint logout **SHOULD** diuji dengan user yang sudah authenticated.
3. Endpoint Sanctum **MUST** memakai `Sanctum::actingAs($user)` bila menguji middleware `auth:sanctum`.
4. Session/web guard **MAY** memakai `$this->actingAs($user, 'web')`.
5. Password test **MUST** dibuat dengan `Hash::make()` atau factory yang memang melakukan hashing.
6. Test auth **MUST** memverifikasi status code, response shape, dan side effect penting seperti `last_login_at`.
7. Test invalid credential **MUST** memastikan response tidak membocorkan apakah email atau password yang salah.
8. Agent **MUST NOT** memakai credential dari `.env`.

Contoh Sanctum:

```php
use Laravel\Sanctum\Sanctum;

Sanctum::actingAs($user);

$response = $this->getJson('/api/v1/auth/me');

$response->assertOk();
```

---

## 12. Google OAuth Testing Rules

Google OAuth menggunakan `Laravel\Socialite\Socialite`.

Aturan:

1. Agent **MUST NOT** melakukan request nyata ke Google.
2. Agent **MUST** mock Socialite untuk callback.
3. Agent **MUST** menguji minimal:
   - user baru dibuat ketika email/google_id belum ada;
   - user existing di-update ketika email sudah ada;
   - user existing ditemukan lewat `google_id`;
   - user di-login ke web guard;
   - redirect callback mengarah ke frontend callback URL.
4. Agent **MUST** membuat role default yang dibutuhkan sebelum memanggil `GoogleAuthService`.
5. Agent **MUST** mengontrol waktu dengan `travelTo()` atau `Carbon::setTestNow()` bila mengassert `email_verified_at` atau `last_login_at`.

---

## 13. Service Test Rules

Service berada di:

```text
backend/app/Features/<Feature>/Services
```

Aturan:

1. Service test **MUST** berada di `tests/Unit/Features/<Feature>/Services`.
2. Service test **MUST** menguji business rule utama, bukan hanya memanggil method.
3. Dependency service **SHOULD** di-mock bila dependency tersebut bukan behavior yang sedang diuji.
4. Jika service memakai facade Laravel seperti `Auth`, `Hash`, `Log`, atau `DB`, agent **MUST** memutuskan apakah masih layak unit test atau lebih tepat menjadi feature/integration test.
5. Exception yang disengaja **MUST** diuji dengan `expect(fn () => ...)->toThrow(...)`.
6. Side effect penting **MUST** diuji: update database, login guard, event, atau log bila relevan.

Contoh:

```php
expect(fn () => $service->login($loginData))
    ->toThrow(InvalidCredentialsException::class);
```

---

## 14. Controller dan Route Test Rules

Controller berada di:

```text
backend/app/Features/<Feature>/Http/Controllers
```

Controller dan route **MUST** diuji sebagai Feature test, bukan Unit test.

Aturan:

1. Test controller **MUST** memanggil route HTTP, bukan memanggil method controller langsung.
2. Test **MUST** memakai URL yang sesuai dengan route aktual di `backend/routes/api/v1`.
3. Test **SHOULD** memverifikasi route name bila route name penting untuk integrasi.
4. Test **MUST** memverifikasi status code.
5. Test JSON **MUST** memverifikasi shape response dengan `assertJsonStructure`, `assertJsonPath`, atau expectation spesifik.
6. Validation error **MUST** diuji dengan status `422`.
7. Unauthorized behavior **MUST** diuji dengan status `401` atau `403` sesuai middleware.
8. Server error branch **SHOULD NOT** diuji dengan memaksa generic `Throwable` kecuali branch tersebut berisi behavior penting.

Contoh:

```php
$response = $this->postJson('/api/v1/auth/login', []);

$response->assertStatus(422)
    ->assertJsonValidationErrors(['email', 'password']);
```

---

## 15. Form Request Validation Test Rules

Form request berada di:

```text
backend/app/Features/<Feature>/Http/Requests
```

Aturan:

1. Validation rule **SHOULD** diuji melalui endpoint yang memakai request tersebut.
2. Test **MUST** mencakup required fields.
3. Test **MUST** mencakup invalid format, seperti email tidak valid.
4. Test **SHOULD** mencakup boundary value untuk angka, tanggal, status, dan enum.
5. Test **MUST** memverifikasi key error, bukan hanya status `422`.
6. Agent **MUST NOT** menduplikasi seluruh logic validator di test. Test harus mengassert behavior.

---

## 16. Repository Test Rules

Repository berada di:

```text
backend/app/Features/<Feature>/Repositories
```

Aturan:

1. Repository Eloquent **MAY** diuji dengan database in-memory.
2. Test repository **MUST** memakai `RefreshDatabase`.
3. Test **MUST** membuktikan query behavior, misalnya menemukan user berdasarkan email.
4. Test **MUST** mencakup not found behavior sesuai kontrak method.
5. Jika kontrak menyatakan return nullable, repository **MUST** mengembalikan `null` saat data tidak ada dan test harus membuktikannya.
6. Jika implementasi memakai `firstOrFail()`, test **MUST** mengassert exception atau agent **SHOULD** memperbaiki kontrak/implementasi bila tidak selaras.

Catatan proyek:

`UserRepositoryContract::findByEmail()` perlu diperiksa sebelum membuat test karena contract dan implementation harus konsisten soal return `User`, `?User`, atau exception.

---

## 17. Model Test Rules

Model berada di:

```text
backend/app/Features/<Feature>/Models
```

Aturan:

1. Test model **SHOULD** fokus pada behavior model: relationship, casts, fillable, hidden, generated UUID, soft delete, dan scope bila ada.
2. Relationship **MUST** diuji bila dipakai oleh endpoint atau service.
3. Cast tanggal **SHOULD** diuji jika field tanggal diproses dalam business logic.
4. Hidden fields **SHOULD** diuji bila model dikembalikan dalam JSON response.
5. Soft delete **MUST** diuji untuk model yang memakai `SoftDeletes` bila delete behavior diimplementasikan.
6. Agent **MUST NOT** menguji fitur internal Laravel secara berlebihan.

Contoh assertion model:

```php
expect($user->public_uuid)->not->toBeNull();
expect($user->password)->not->toBe('password');
```

---

## 18. DTO dan Enum Test Rules

DTO berada di:

```text
backend/app/Features/<Feature>/DTOs
```

Enum berada di:

```text
backend/app/Features/<Feature>/Enums
```

Aturan:

1. DTO test **MUST** menguji mapping dari sumber data yang tersedia, misalnya request validated data atau Socialite user.
2. DTO test **MUST** menguji field wajib dan nullable yang berdampak pada behavior.
3. Enum test **SHOULD** menguji value yang dipakai sebagai kontrak database/API.
4. Agent **MUST NOT** membuat test trivial yang hanya mengulang constructor tanpa behavior.

---

## 19. Time, Date, dan UUID Rules

1. Agent **MUST** mengontrol waktu ketika mengassert timestamp.
2. Gunakan `travelTo()` di Feature test atau `Carbon::setTestNow()` bila sesuai.
3. Agent **MUST** membersihkan fixed time setelah test bila memakai `Carbon::setTestNow()`.
4. Agent **SHOULD NOT** mengassert exact UUID kecuali UUID diberikan eksplisit.
5. Untuk generated UUID, cukup assert tidak null dan valid UUID bila penting.

Contoh:

```php
$this->travelTo(now()->startOfSecond());
```

---

## 20. JSON Response Rules

Untuk endpoint API, test **MUST** memverifikasi:

1. Status code.
2. Struktur JSON.
3. Field penting.
4. Side effect database bila endpoint menulis data.
5. Auth atau authorization bila endpoint protected.

Gunakan assertion Laravel:

```php
$response->assertOk()
    ->assertJsonPath('success', true)
    ->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'user',
        ],
    ]);
```

Aturan:

1. Agent **MUST NOT** mengassert seluruh response besar bila hanya beberapa field yang penting.
2. Agent **MUST** mengassert field yang menjadi kontrak frontend.
3. Agent **MUST** memastikan field sensitive seperti `password` dan `remember_token` tidak muncul di response user.

---

## 21. Security Test Rules

Agent **MUST** menambahkan test keamanan ketika menyentuh area berikut:

- Auth.
- Authorization.
- Ownership resource.
- Password.
- Token.
- OAuth.
- File upload.
- Input yang dapat menjadi injection vector.

Minimal security assertions:

1. User tidak authenticated tidak boleh mengakses endpoint protected.
2. User tidak boleh mengakses resource milik user lain bila ownership berlaku.
3. Password tidak boleh muncul di JSON response.
4. Invalid credential tidak boleh memberi pesan berbeda untuk email tidak ditemukan vs password salah.
5. Endpoint OAuth tidak boleh melakukan call eksternal dalam test.
6. Validation harus menolak payload invalid.

---

## 22. Naming Convention

### File test

Gunakan suffix `Test.php`.

```text
UserAuthServiceTest.php
GoogleAuthServiceTest.php
LoginRequestTest.php
AuthRoutesTest.php
CourseControllerTest.php
```

### Test description

Gunakan behavior sentence:

```php
it('returns validation errors when login payload is empty', function () {
    // ...
});

it('creates a new user from a verified Google account', function () {
    // ...
});
```

Hindari:

```php
it('test login', function () {
    // ...
});

it('works', function () {
    // ...
});
```

---

## 23. Test Data Rules

1. Agent **MUST** memakai email domain aman seperti `example.com`.
2. Agent **MUST NOT** memakai data pribadi nyata.
3. Agent **MUST** membuat payload valid secara eksplisit di test atau helper.
4. Agent **SHOULD** membuat helper lokal dalam file test untuk payload yang dipakai berulang.
5. Helper test **MUST** tetap spesifik dan tidak menyembunyikan behavior penting.

Contoh helper lokal:

```php
function validLoginPayload(array $overrides = []): array
{
    return array_merge([
        'email' => 'student@example.com',
        'password' => 'password',
    ], $overrides);
}
```

Helper global di `tests/Pest.php` **MAY** ditambahkan hanya jika dipakai lintas banyak file.

---

## 24. Command Verifikasi

Jalankan dari folder `backend`.

Minimal untuk semua perubahan test:

```bash
php artisan test
```

Untuk menjalankan suite tertentu:

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

Untuk menjalankan satu file:

```bash
php artisan test tests/Unit/Features/Auth/Services/UserAuthServiceTest.php
```

Untuk menjalankan satu filter:

```bash
php artisan test --filter="updates the last login timestamp"
```

Jika tersedia dan relevan, agent **SHOULD** menjalankan formatting:

```bash
vendor/bin/pint
```

Aturan:

1. Agent **MUST** melaporkan command yang dijalankan.
2. Agent **MUST** melaporkan hasilnya: berhasil atau gagal.
3. Jika gagal, agent **MUST** menjelaskan error utama dan memperbaiki bila masih dalam scope.
4. Agent **MUST NOT** mengklaim test pass tanpa menjalankan command.

---

## 25. Alur Wajib Membuat Unit Test

Agent **MUST** mengikuti urutan berikut:

1. Identifikasi class atau behavior yang diminta.
2. Baca file source terkait.
3. Baca dependency yang dipakai class tersebut.
4. Tentukan jenis test: Unit atau Feature.
5. Tentukan lokasi file test.
6. Tentukan scenario utama:
   - success path;
   - validation atau invalid input;
   - not found;
   - unauthorized atau forbidden bila terkait auth;
   - side effect database;
   - exception yang disengaja.
7. Buat data test minimal dengan factory atau mock.
8. Tulis test Pest dengan Arrange, Act, Assert.
9. Jalankan test file terkait.
10. Jika pass, jalankan suite yang relevan.
11. Laporkan ringkasan, file, command, dan hasil.

---

## 26. Scenario Minimum per Layer

### Service

- Success path.
- Dependency dipanggil dengan argument benar.
- Invalid input atau dependency not found.
- Exception domain.
- Side effect penting.

### Controller/API

- Status success.
- JSON contract.
- Validation error.
- Unauthorized atau forbidden bila protected.
- Database side effect.
- Sensitive fields tidak bocor.

### Repository

- Data ditemukan.
- Data tidak ditemukan.
- Query filter sesuai contract.
- Soft-deleted data disertakan atau dikecualikan sesuai behavior.

### Model

- Factory valid.
- Relationship utama.
- Cast penting.
- Hidden sensitive attributes.
- Generated attributes seperti UUID.
- Soft delete bila ada.

### DTO

- Mapping input valid.
- Nullable field.
- Type conversion.
- Missing atau invalid source data bila method DTO menanganinya.

### Enum

- Value sesuai database/API contract.
- Helper method bila ada.

---

## 27. Larangan Keras

Agent **MUST NOT**:

1. Menulis test hanya untuk menaikkan jumlah test tanpa assertion bermakna.
2. Menulis test yang bergantung pada `.env` lokal.
3. Menulis test yang membutuhkan network eksternal.
4. Menulis test yang bergantung pada urutan eksekusi test.
5. Mengubah source code produksi hanya agar test mudah dibuat, kecuali ada bug nyata yang ditemukan dan dijelaskan.
6. Mock class yang sedang diuji.
7. Menguji private method langsung.
8. Membuat snapshot besar untuk response API sederhana.
9. Menggunakan `sleep()` untuk menunggu waktu.
10. Meninggalkan `dump()`, `dd()`, `ray()`, `var_dump()`, atau log debug.
11. Menggunakan data pribadi nyata.
12. Menghapus test existing tanpa alasan jelas.
13. Mengubah `phpunit.xml` tanpa kebutuhan kuat.
14. Mengaktifkan `RefreshDatabase` global tanpa mempertimbangkan dampak suite existing.
15. Membuat helper global untuk kebutuhan satu file.
16. Membuat test yang hanya mengulang implementasi baris demi baris.
17. Mengabaikan failing test yang relevan.

---

## 28. Definition of Done

Unit test dianggap selesai hanya jika:

- [ ] File test berada di lokasi yang sesuai.
- [ ] Nama file berakhir dengan `Test.php`.
- [ ] Test memakai Pest style.
- [ ] Test memiliki assertion bermakna.
- [ ] Test mencakup success path.
- [ ] Test mencakup failure path yang relevan.
- [ ] Test database memakai `RefreshDatabase`.
- [ ] Test tidak melakukan network request nyata.
- [ ] Test tidak bergantung pada `.env` lokal.
- [ ] Test tidak meninggalkan debug output.
- [ ] Test menggunakan factory atau mock dengan benar.
- [ ] Sensitive data tidak bocor pada assertion response.
- [ ] Command test relevan sudah dijalankan.
- [ ] Hasil test dilaporkan dengan jujur.

---

## 29. Format Laporan Agent Setelah Menulis Test

Setelah membuat atau mengubah test, agent **SHOULD** melaporkan:

```text
Ringkasan:
- Menambahkan unit test untuk UserAuthService.
- Mencakup login sukses, credential invalid, dan update last_login_at.

File:
- backend/tests/Unit/Features/Auth/Services/UserAuthServiceTest.php

Validasi:
- php artisan test tests/Unit/Features/Auth/Services/UserAuthServiceTest.php: berhasil
- php artisan test --testsuite=Unit: berhasil

Catatan:
- Repository dimock karena fokus test adalah behavior service, bukan query database.
```

Jika ada test gagal yang tidak diperbaiki:

```text
Validasi:
- php artisan test: gagal

Penyebab:
- Migration courses gagal di SQLite karena ...

Status:
- Belum diperbaiki karena berada di luar scope perubahan test ini.
```

---

## 30. Checklist Internal Sebelum Menulis Test

Sebelum membuat test, agent harus bertanya secara internal:

1. Behavior apa yang benar-benar perlu dibuktikan?
2. Apakah ini unit test atau feature test?
3. File source dan dependency apa yang harus dibaca?
4. Apakah perlu database?
5. Apakah perlu `RefreshDatabase`?
6. Apakah perlu mock, fake, atau factory?
7. Apa success path minimal?
8. Apa failure path yang paling penting?
9. Apakah ada auth, authorization, atau security implication?
10. Command apa yang harus dijalankan untuk membuktikan test berhasil?

Jika agent tidak yakin memilih unit atau feature test, pilih test yang paling merepresentasikan behavior pengguna tanpa membuat mocking berlebihan.
