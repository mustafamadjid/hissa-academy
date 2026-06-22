# Frontend AI Agent Rules — HISSA

Dokumen ini adalah aturan wajib bagi AI agent ketika menambah, mengubah, atau melakukan refactor fitur pada frontend HISSA.

Frontend HISSA menggunakan:

- Vue 3
- TypeScript
- Vite
- Vue Router
- Pinia
- Tailwind CSS
- Feature-based architecture

Kata kunci normatif yang digunakan:

- **MUST**: wajib dipatuhi.
- **MUST NOT**: dilarang.
- **SHOULD**: sangat disarankan; penyimpangan harus memiliki alasan teknis.
- **MAY**: opsional.

---

## 1. Prinsip Utama

1. Agent **MUST** mempertahankan struktur feature-based yang sudah ada.
2. Agent **MUST** menempatkan kode berdasarkan tanggung jawab dan domain bisnisnya.
3. Agent **MUST NOT** membuat struktur baru hanya karena pola tersebut umum digunakan di proyek lain.
4. Agent **MUST** memeriksa file dan konvensi yang sudah ada sebelum membuat implementasi baru.
5. Agent **MUST** memilih perubahan paling kecil yang memenuhi kebutuhan fitur.
6. Agent **MUST NOT** memindahkan kode lintas folder tanpa alasan arsitektural yang jelas.
7. Agent **MUST NOT** mencampurkan UI, komunikasi API, state global, routing, validasi, dan utility dalam satu file.
8. Agent **MUST** menjaga dependency tetap satu arah dan menghindari circular dependency.
9. Agent **MUST** menggunakan TypeScript secara ketat dan **MUST NOT** menggunakan `any` tanpa alasan yang terdokumentasi.
10. Agent **MUST** memastikan hasil akhir lolos type-check dan build.

---

## 2. Struktur Direktori yang Wajib Dipertahankan

```text
src/
|-- app/
|   |-- router/
|   |   |-- index.ts
|   |   `-- routes.ts
|   |-- App.vue
|   `-- pinia.ts
|-- assets/
|   |-- images/
|   `-- styles/
|       `-- main.css
|-- features/
|   |-- auth/
|   |-- certificate/
|   |-- course-catalog/
|   |-- dashboard/
|   |-- learning/
|   |-- profile/
|   `-- quiz/
|-- layouts/
|-- shared/
|   |-- api/
|   |-- components/
|   |   |-- navigation/
|   |   `-- ui/
|   |-- composables/
|   |-- constants/
|   |-- types/
|   `-- utils/
`-- main.ts
```

Agent **MUST NOT** membuat folder global baru seperti berikut tanpa kebutuhan yang benar-benar tidak dapat ditampung oleh struktur saat ini:

```text
src/services/
src/helpers/
src/models/
src/pages/
src/components/
src/store/
src/modules/
```

Gunakan folder yang sudah tersedia:

- Service HTTP fitur → `features/<feature>/api`
- Helper global → `shared/utils`
- Type global → `shared/types`
- Page → `features/<feature>/pages`
- Component global → `shared/components`
- Store fitur → `features/<feature>/stores`

---

## 3. Batas Tanggung Jawab Folder

### 3.1 `src/app`

Digunakan hanya untuk konfigurasi inti aplikasi.

Isi yang diperbolehkan:

- Root component.
- Registrasi plugin.
- Konfigurasi Pinia.
- Konfigurasi Vue Router.
- Route registry.
- Application bootstrap.

Isi yang dilarang:

- Business logic fitur.
- API request fitur.
- Component khusus fitur.
- Validasi form fitur.
- Store domain.

---

### 3.2 `src/features/<feature>`

Setiap domain bisnis harus ditempatkan di dalam satu fitur.

Struktur standar:

```text
features/<feature>/
|-- api/
|-- components/
|-- composables/
|-- pages/
|-- stores/
|-- types/
`-- validation/
```

Agent **MUST** membuat file hanya pada subfolder yang benar-benar diperlukan. Folder yang tersedia tidak berarti setiap fitur harus memiliki file pada seluruh subfolder.

Contoh:

```text
features/auth/
|-- api/
|   `-- auth.api.ts
|-- components/
|   `-- GoogleLoginButton.vue
|-- composables/
|   `-- useGoogleAuth.ts
|-- pages/
|   `-- LoginPage.vue
|-- stores/
|   `-- auth.store.ts
|-- types/
|   `-- auth.types.ts
`-- validation/
    `-- profile-completion.schema.ts
```

---

### 3.3 `src/layouts`

Layout digunakan sebagai pembungkus halaman.

Layout MAY menangani:

- Struktur navbar, sidebar, footer, atau content area.
- Slot halaman.
- Navigasi tingkat layout.
- Tampilan loading autentikasi tingkat aplikasi bila diperlukan.

Layout **MUST NOT** menangani:

- Request API domain.
- Perhitungan progres belajar.
- Jawaban kuis.
- Penerbitan sertifikat.
- Validasi form fitur.
- Business rules.

Nama yang disarankan:

```text
DefaultLayout.vue
AuthLayout.vue
DashboardLayout.vue
LearningLayout.vue
```

---

### 3.4 `src/shared`

`shared` hanya untuk kode reusable lintas fitur.

Kode di `shared`:

- **MUST** bersifat generik.
- **MUST NOT** bergantung pada satu domain bisnis.
- **MUST NOT** melakukan import dari `features`.
- **MUST NOT** mengandung aturan bisnis khusus course, quiz, certificate, atau auth.

Contoh yang benar:

```text
shared/api/http-client.ts
shared/components/ui/BaseButton.vue
shared/components/ui/BaseModal.vue
shared/components/navigation/AppNavbar.vue
shared/composables/useDebounce.ts
shared/constants/http-status.ts
shared/types/pagination.types.ts
shared/utils/format-date.ts
```

Contoh yang salah:

```text
shared/utils/calculate-quiz-score.ts
shared/components/CertificateCard.vue
shared/composables/useCourseProgress.ts
```

Kode tersebut harus berada di fitur terkait.

---

## 4. Aturan Dependency

Dependency yang diperbolehkan:

```text
main.ts
  -> app
  -> layouts
  -> features
  -> shared
```

Aturan wajib:

1. `shared` **MUST NOT** mengimpor dari `features`.
2. `shared` **MUST NOT** mengimpor dari `layouts`.
3. Feature **MAY** mengimpor dari `shared`.
4. Layout **MAY** mengimpor dari `shared`.
5. `app/router` **MAY** mengimpor page dan layout untuk registrasi route.
6. Direct import antar-feature **SHOULD NOT** dilakukan.
7. Apabila dua fitur membutuhkan kode yang sama, agent harus menentukan apakah kode tersebut:
   - benar-benar generik dan layak dipindahkan ke `shared`; atau
   - merupakan domain capability yang tetap dimiliki satu fitur.
8. Agent **MUST NOT** memindahkan business logic ke `shared` hanya untuk menghindari import antar-feature.
9. Circular dependency **MUST NOT** ada.

Contoh import yang dilarang:

```ts
// shared/composables/useSession.ts
import { useAuthStore } from '@/features/auth/stores/auth.store'
```

Contoh import yang diperbolehkan:

```ts
// features/auth/api/auth.api.ts
import { httpClient } from '@/shared/api/http-client'
```

Agent **MUST** mengikuti alias import yang sudah dikonfigurasi pada `vite.config.ts` dan `tsconfig*.json`. Agent **MUST NOT** membuat alias baru tanpa memperbarui kedua konfigurasi tersebut.

---

## 5. Aturan Component Vue

### 5.1 Component khusus fitur

Simpan di:

```text
features/<feature>/components
```

Component fitur:

- **MUST** memiliki tanggung jawab yang spesifik.
- **SHOULD** menerima data melalui props.
- **SHOULD** mengirim interaksi melalui emits.
- **MUST NOT** melakukan request HTTP langsung.
- **MUST NOT** mengakses endpoint menggunakan `fetch` atau Axios secara langsung.
- **MUST NOT** berisi logic routing kompleks.
- **SHOULD** memindahkan logic reusable ke composable.
- **SHOULD** tetap presentasional jika memungkinkan.

---

### 5.2 Component bersama

Simpan di:

```text
shared/components/ui
shared/components/navigation
```

Component bersama:

- **MUST** netral terhadap domain.
- **MUST NOT** memiliki pengetahuan tentang course, quiz, lesson, certificate, atau user progress.
- **MUST** dikonfigurasi menggunakan props, slots, dan emits.
- **MUST NOT** melakukan request API.
- **MUST NOT** mengakses store fitur.

Contoh:

```vue
<BaseButton :loading="isSubmitting" @click="submit">
  Simpan
</BaseButton>
```

---

### 5.3 Naming component

Gunakan PascalCase:

```text
LoginPage.vue
CourseCard.vue
QuizQuestion.vue
CertificateStatusBadge.vue
BaseButton.vue
AppSidebar.vue
```

Hindari nama terlalu umum pada component fitur:

```text
Card.vue
Form.vue
List.vue
Item.vue
```

Gunakan nama yang menunjukkan konteks:

```text
CourseCard.vue
ProfileForm.vue
LessonList.vue
QuizOptionItem.vue
```

---

## 6. Aturan Page

Page disimpan di:

```text
features/<feature>/pages
```

Page adalah route-level component.

Page:

- **MUST** menjadi entry point route.
- **SHOULD** mengorkestrasi component dan composable.
- **MUST NOT** menjadi file monolitik.
- **MUST NOT** berisi implementasi HTTP client.
- **MUST NOT** menduplikasi validation rule.
- **SHOULD** memecah bagian UI besar menjadi feature components.
- **SHOULD** menggunakan lazy-loaded route.

Contoh nama:

```text
LoginPage.vue
CourseCatalogPage.vue
CourseDetailPage.vue
LearningPage.vue
QuizAttemptPage.vue
CertificateVerificationPage.vue
```

---

## 7. Aturan Composable

Composable disimpan di:

```text
features/<feature>/composables
shared/composables
```

Gunakan prefix `use`:

```text
useLogin.ts
useCourseCatalog.ts
useLessonProgress.ts
useQuizAttempt.ts
useDebounce.ts
```

Composable fitur digunakan untuk:

- Mengorkestrasi state UI.
- Menghubungkan page/component dengan API atau store.
- Menangani lifecycle.
- Menangani loading, success, dan error state.
- Mengemas logic Composition API yang reusable dalam satu fitur.

Composable bersama digunakan hanya untuk logic generik.

Composable:

- **MUST NOT** merender UI.
- **MUST NOT** berisi template.
- **MUST NOT** mengandung hardcoded endpoint.
- **MUST** mengembalikan API yang jelas.
- **MUST** membersihkan event listener, interval, atau observer pada lifecycle yang tepat.
- **MUST NOT** menyimpan state global menggunakan variable module biasa jika state tersebut seharusnya dikelola Pinia.

Contoh return composable:

```ts
return {
  courses: readonly(courses),
  isLoading: readonly(isLoading),
  error: readonly(error),
  fetchCourses,
}
```

---

## 8. Aturan Pinia Store

Store disimpan di:

```text
features/<feature>/stores
```

Gunakan nama:

```text
auth.store.ts
course.store.ts
learning.store.ts
quiz.store.ts
```

Store function:

```ts
useAuthStore
useCourseStore
useLearningStore
useQuizStore
```

Pinia digunakan hanya ketika state:

- Dipakai oleh beberapa component atau page.
- Perlu dipertahankan selama navigasi.
- Merupakan state session aplikasi.
- Memiliki lifecycle yang lebih panjang daripada satu component.

Pinia **MUST NOT** digunakan untuk semua local state.

State lokal seperti modal terbuka, tab aktif, atau input sementara **SHOULD** tetap berada di component atau composable apabila tidak dibagikan secara global.

Store:

- **MUST** memiliki state yang typed.
- **MUST** memisahkan state, getters/computed, dan actions secara jelas.
- **MUST NOT** melakukan manipulasi DOM.
- **MUST NOT** menggunakan Vue Router sebagai tempat business logic.
- **MUST NOT** menyimpan object response mentah jika hanya sebagian data yang diperlukan.
- **SHOULD** memanggil fungsi API dari folder `api`, bukan menulis request langsung.
- **MUST** mengelola loading dan error jika request dilakukan oleh action store.
- **MUST NOT** menyimpan data sensitif di `localStorage` tanpa kebutuhan dan mitigasi keamanan yang jelas.

---

## 9. Aturan API Layer

API fitur disimpan di:

```text
features/<feature>/api
```

Konfigurasi HTTP global disimpan di:

```text
shared/api
```

Contoh:

```text
shared/api/http-client.ts
shared/api/api-error.ts
features/auth/api/auth.api.ts
features/learning/api/learning.api.ts
features/quiz/api/quiz.api.ts
```

Aturan wajib:

1. Component dan page **MUST NOT** memanggil `fetch`, Axios, atau endpoint secara langsung.
2. Base URL, credential policy, interceptor, CSRF handling, dan normalisasi error global harus berada di `shared/api`.
3. Endpoint khusus domain harus berada di `features/<feature>/api`.
4. Fungsi API **MUST** memiliki input dan output TypeScript yang eksplisit.
5. URL endpoint **MUST NOT** tersebar di component.
6. Fungsi API **SHOULD** memiliki satu tanggung jawab.
7. Raw API response harus dipetakan jika bentuknya tidak cocok dengan kebutuhan frontend.
8. Error **MUST NOT** diabaikan.
9. Agent **MUST NOT** menambahkan library HTTP baru jika proyek sudah memiliki HTTP client.
10. Untuk autentikasi berbasis cookie, agent **MUST** mempertahankan konfigurasi credentials dan CSRF yang sudah digunakan proyek.

Contoh:

```ts
import { httpClient } from '@/shared/api/http-client'
import type { Course, CourseListParams } from '../types/course.types'

export async function getCourses(
  params: CourseListParams,
): Promise<Course[]> {
  const response = await httpClient.get('/api/v1/courses', { params })

  return response.data.data
}
```

---

## 10. Aturan TypeScript

Type fitur disimpan di:

```text
features/<feature>/types
```

Type lintas fitur disimpan di:

```text
shared/types
```

Aturan wajib:

1. `any` **MUST NOT** digunakan kecuali integrasi pihak ketiga tidak menyediakan type yang memadai.
2. Data eksternal yang belum tervalidasi **SHOULD** menggunakan `unknown`, bukan `any`.
3. Props, emits, response API, payload API, dan store state **MUST** typed.
4. Type domain fitur **MUST NOT** diletakkan di `shared/types`.
5. Agent **MUST NOT** membuat type global hanya karena digunakan oleh dua file dalam fitur yang sama.
6. Gunakan union type untuk finite states.
7. Gunakan `null` atau optional property secara konsisten mengikuti kontrak API.
8. Hindari duplikasi interface dengan struktur identik.
9. Pisahkan API DTO dari UI model jika keduanya memiliki bentuk atau kebutuhan yang berbeda.
10. Type assertion `as` **SHOULD NOT** digunakan untuk menutupi error desain type.

Contoh:

```ts
export type LessonProgressStatus =
  | 'not_started'
  | 'in_progress'
  | 'completed'

export interface LessonProgress {
  lessonId: string
  watchedSeconds: number
  percentage: number
  status: LessonProgressStatus
}
```

---

## 11. Aturan Validation

Validation fitur disimpan di:

```text
features/<feature>/validation
```

Aturan wajib:

1. Validation rule **MUST NOT** ditulis berulang di beberapa component.
2. Schema harus dikelompokkan berdasarkan form atau use case.
3. Pesan error harus dapat ditampilkan secara konsisten.
4. Frontend validation digunakan untuk user experience.
5. Frontend validation **MUST NOT** dianggap sebagai pengganti validasi backend.
6. Agent **MUST NOT** menambahkan library validation baru apabila proyek sudah menggunakan library tertentu.
7. Jika belum ada library validation, agent harus mengikuti pola existing sebelum memperkenalkan dependency baru.

Contoh nama:

```text
login.schema.ts
profile.schema.ts
quiz-answer.schema.ts
certificate-verification.schema.ts
```

---

## 12. Aturan Routing

Routing dikonfigurasi di:

```text
src/app/router/index.ts
src/app/router/routes.ts
```

Aturan wajib:

1. Page baru **MUST** didaftarkan pada router.
2. Route component **SHOULD** menggunakan dynamic import.
3. Route name **MUST** unik.
4. Path **MUST** konsisten dan menggunakan kebab-case.
5. Auth guard, role guard, dan route meta harus ditangani secara terpusat.
6. Component **MUST NOT** menentukan authorization hanya dengan menyembunyikan tombol.
7. Authorization frontend hanya untuk UX; backend tetap menjadi sumber otorisasi final.
8. Route string yang digunakan berulang **SHOULD** ditempatkan di constants.
9. Agent **MUST NOT** menaruh logic API di navigation guard.
10. Redirect harus mencegah loop.

Contoh:

```ts
{
  path: '/courses',
  name: 'course-catalog',
  component: () =>
    import('@/features/course-catalog/pages/CourseCatalogPage.vue'),
  meta: {
    requiresAuth: true,
  },
}
```

---

## 13. Aturan Styling dan Tailwind CSS

1. Agent **MUST** menggunakan Tailwind CSS sesuai konfigurasi proyek.
2. Style global hanya ditempatkan di `src/assets/styles/main.css`.
3. Style khusus component **MAY** menggunakan `<style scoped>` jika utility Tailwind tidak memadai.
4. Agent **MUST NOT** menambahkan CSS global untuk kebutuhan satu component.
5. Agent **SHOULD** menggunakan shared UI component untuk pola tampilan yang berulang.
6. Agent **MUST NOT** menduplikasi kombinasi class kompleks berulang kali jika dapat diekstrak menjadi component.
7. Agent **MUST** menjaga responsive behavior.
8. Agent **MUST** mempertahankan design language yang sudah ada.
9. Agent **MUST NOT** memperkenalkan framework UI baru tanpa permintaan eksplisit.
10. Agent **MUST NOT** mengubah konfigurasi Tailwind untuk menyelesaikan masalah lokal yang dapat diselesaikan di component.

---

## 14. Aturan Asset

- Aset yang di-import oleh source code disimpan di `src/assets`.
- File yang harus tersedia langsung dari root URL disimpan di `public`.
- Agent **MUST NOT** menyimpan source asset di `dist`.
- Agent **MUST NOT** mengedit file hasil build.
- Nama file aset menggunakan kebab-case.
- Aset yang sudah ada **SHOULD** digunakan kembali sebelum menambahkan duplikasi.

Contoh:

```text
src/assets/images/hissa-logo.svg
src/assets/images/course-placeholder.webp
public/favicon.ico
```

---

## 15. Aturan Environment dan Konfigurasi

1. Secret **MUST NOT** ditulis di source code.
2. Environment variable frontend harus menggunakan pola Vite yang berlaku.
3. Agent **MUST NOT** menaruh token rahasia di environment frontend karena nilai frontend dapat dibaca pengguna.
4. Nilai konfigurasi yang bukan rahasia dan digunakan lintas fitur **SHOULD** ditempatkan di `shared/constants`.
5. Agent **MUST** memperbarui deklarasi type environment jika menambahkan variable baru.
6. Agent **MUST NOT** hardcode base URL berbeda pada masing-masing fitur.

---

## 16. Aturan Error, Loading, dan Empty State

Setiap operasi asynchronous yang terlihat pengguna **MUST** mempertimbangkan:

- Initial loading.
- Refetch loading bila berbeda.
- Success state.
- Validation error.
- API error.
- Empty state.
- Retry behavior bila relevan.

Agent **MUST NOT**:

- Menelan exception tanpa penanganan.
- Hanya mencetak error menggunakan `console.log`.
- Menampilkan raw stack trace kepada pengguna.
- Membiarkan tombol submit aktif saat request duplikat dapat terjadi.

Agent **SHOULD**:

- Menormalkan pesan error.
- Menonaktifkan aksi selama request kritis.
- Menampilkan fallback yang dapat dipahami pengguna.
- Mempertahankan data lama ketika refetch gagal apabila aman.

---

## 17. Aturan Accessibility

Agent **MUST**:

1. Menggunakan elemen HTML semantik.
2. Menghubungkan `<label>` dengan input.
3. Memberikan `alt` yang sesuai untuk gambar bermakna.
4. Memastikan aksi dapat digunakan melalui keyboard.
5. Menggunakan `<button>` untuk aksi, bukan `<div>` dengan click handler.
6. Menjaga focus state.
7. Menambahkan ARIA hanya ketika semantic HTML tidak cukup.
8. Memastikan modal memiliki focus management jika membuat modal custom.
9. Tidak hanya menggunakan warna untuk menyampaikan status.
10. Memastikan loading state dapat dipahami assistive technology bila relevan.

---

## 18. Aturan Keamanan Frontend

1. Agent **MUST NOT** menganggap data frontend terpercaya.
2. Agent **MUST NOT** menggunakan `v-html` untuk data eksternal tanpa sanitasi yang aman.
3. Agent **MUST NOT** menyimpan access token sensitif di `localStorage` apabila proyek menggunakan cookie/session.
4. Agent **MUST NOT** menampilkan data sensitif pada console.
5. Agent **MUST** mempertahankan proteksi CSRF yang digunakan aplikasi.
6. Agent **MUST NOT** mengandalkan frontend untuk validasi role atau ownership.
7. Redirect URL dari parameter eksternal **MUST** divalidasi untuk mencegah open redirect.
8. Agent **MUST NOT** menambahkan dependency tanpa memeriksa kebutuhan dan dampak ke bundle serta keamanan.

---

## 19. Naming Convention

### Folder

Gunakan kebab-case:

```text
course-catalog
user-progress
certificate-verification
```

### Vue component dan page

Gunakan PascalCase:

```text
CourseCard.vue
CourseCatalogPage.vue
CertificateVerificationForm.vue
```

### Composable

Gunakan camelCase dengan prefix `use`:

```text
useCourseCatalog.ts
useLessonProgress.ts
```

### Store

Nama file:

```text
auth.store.ts
learning.store.ts
```

Nama function:

```ts
useAuthStore
useLearningStore
```

### API

```text
auth.api.ts
course.api.ts
quiz.api.ts
```

### Type

```text
auth.types.ts
course.types.ts
pagination.types.ts
```

### Validation

```text
login.schema.ts
profile.schema.ts
```

### Utility

Gunakan nama berbasis aksi:

```text
format-date.ts
calculate-percentage.ts
parse-api-error.ts
```

---

## 20. Alur Implementasi Fitur Baru

Agent **MUST** mengikuti urutan berikut.

### Langkah 1 — Analisis

Sebelum menulis kode:

1. Identifikasi domain fitur.
2. Periksa apakah fitur sudah memiliki folder.
3. Cari component, composable, store, type, API function, dan utility yang sudah ada.
4. Periksa kontrak API.
5. Tentukan state lokal dan state global.
6. Tentukan route dan layout yang digunakan.
7. Tentukan validation rule.
8. Identifikasi shared component yang dapat digunakan kembali.

### Langkah 2 — Rencana File

Agent harus menentukan file yang akan:

- dibuat;
- diubah;
- dihapus;
- dipindahkan.

Agent **MUST NOT** membuat file placeholder kosong.

### Langkah 3 — Type

Definisikan lebih dahulu bila fitur memiliki:

- API response.
- Request payload.
- Domain state.
- Props kompleks.
- Status union.
- Pagination.

### Langkah 4 — API

Tambahkan fungsi request pada folder API fitur.

### Langkah 5 — State dan Logic

Pilih salah satu:

- Local state di page/component.
- Feature composable.
- Pinia store.

Jangan menggunakan Pinia apabila local state sudah cukup.

### Langkah 6 — UI

Bangun component fitur dan gunakan shared UI component yang sudah tersedia.

### Langkah 7 — Page

Susun route-level page sebagai orchestrator.

### Langkah 8 — Route

Daftarkan route baru dan terapkan meta/guard yang diperlukan.

### Langkah 9 — Verifikasi

Jalankan minimal:

```bash
npm run type-check
npm run build
```

Jalankan lint atau test hanya jika script tersebut tersedia pada `package.json`.

---

## 21. Alur Perubahan Fitur yang Sudah Ada

Saat memodifikasi fitur:

1. Agent **MUST** membaca implementasi terkait sebelum mengubahnya.
2. Agent **MUST** mempertahankan public behavior kecuali perubahan memang diminta.
3. Agent **MUST NOT** melakukan refactor besar bersamaan dengan perubahan kecil tanpa alasan.
4. Agent **MUST** menghindari perubahan lintas fitur yang tidak terkait.
5. Agent **MUST** memperbarui type jika kontrak data berubah.
6. Agent **MUST** memperbarui loading, error, dan empty state jika flow asynchronous berubah.
7. Agent **MUST** memeriksa route guard dan authorization jika fitur menyentuh akses pengguna.
8. Agent **MUST** menghapus import, state, dan file yang benar-benar tidak digunakan setelah perubahan.
9. Agent **MUST** menjalankan type-check dan build.

---

## 22. Larangan Keras

Agent **MUST NOT**:

1. Menaruh semua logic di `.vue` page.
2. Memanggil API langsung dari template atau event handler component.
3. Membuat satu store global untuk seluruh aplikasi.
4. Menaruh semua type di satu file global.
5. Memindahkan domain logic ke `shared`.
6. Membuat helper bernama umum tanpa domain atau tujuan jelas.
7. Menggunakan `any` untuk mempercepat penyelesaian error.
8. Mengabaikan error TypeScript.
9. Mengedit `dist` atau `node_modules`.
10. Menambahkan dependency tanpa kebutuhan nyata.
11. Membuat duplicate component yang sudah tersedia.
12. Membuat duplicate endpoint wrapper.
13. Hardcode base URL API.
14. Hardcode role, status, atau route name berulang kali.
15. Menyimpan secret di frontend.
16. Menggunakan `v-html` untuk konten eksternal tanpa sanitasi.
17. Menggunakan local storage untuk session tanpa mengikuti desain autentikasi proyek.
18. Menyembunyikan tombol sebagai satu-satunya authorization control.
19. Membuat import melingkar.
20. Mengubah struktur utama proyek tanpa permintaan eksplisit.

---

## 23. Kriteria Pemindahan Kode ke `shared`

Kode hanya boleh dipindahkan ke `shared` jika seluruh kondisi berikut terpenuhi:

1. Digunakan atau realistis digunakan oleh lebih dari satu fitur.
2. Tidak mengandung istilah atau aturan bisnis khusus satu fitur.
3. API-nya dapat dibuat generik.
4. Tidak mengimpor store, component, composable, atau type domain dari fitur.
5. Pemindahan mengurangi duplikasi nyata, bukan duplikasi hipotetis.
6. Tidak menyebabkan abstraksi lebih kompleks daripada kode asli.

Jika salah satu kondisi tidak terpenuhi, kode tetap berada di fitur asal.

---

## 24. Definition of Done

Fitur dianggap selesai hanya jika:

- [ ] File berada pada folder yang benar.
- [ ] Tidak ada direct API call dari component atau page.
- [ ] Props, emits, API response, payload, dan state sudah typed.
- [ ] Tidak ada penggunaan `any` yang tidak dapat dibenarkan.
- [ ] Tidak ada circular dependency.
- [ ] Tidak ada domain logic di `shared`.
- [ ] Loading state ditangani.
- [ ] Error state ditangani.
- [ ] Empty state ditangani bila relevan.
- [ ] Validation tidak diduplikasi.
- [ ] Route sudah didaftarkan bila fitur memiliki page baru.
- [ ] Authorization UX mengikuti route meta/guard yang berlaku.
- [ ] UI dapat digunakan dengan keyboard.
- [ ] Tidak ada secret atau data sensitif di source/console.
- [ ] Tidak ada import atau file yang tidak digunakan.
- [ ] `npm run type-check` berhasil.
- [ ] `npm run build` berhasil.

---

## 25. Format Laporan Agent Setelah Implementasi

Setelah melakukan perubahan, agent **SHOULD** melaporkan:

1. Ringkasan perubahan.
2. Daftar file yang dibuat atau diubah.
3. Keputusan arsitektural penting.
4. Validasi yang dijalankan.
5. Risiko atau pekerjaan lanjutan yang benar-benar relevan.

Contoh:

```text
Ringkasan:
- Menambahkan halaman katalog course.
- Menambahkan API wrapper dan type response.
- Menggunakan local state melalui composable karena data tidak perlu disimpan global.

File:
- src/features/course-catalog/api/course.api.ts
- src/features/course-catalog/composables/useCourseCatalog.ts
- src/features/course-catalog/pages/CourseCatalogPage.vue
- src/app/router/routes.ts

Validasi:
- npm run type-check: berhasil
- npm run build: berhasil
```

---

## 26. Decision Table Penempatan File

| Kebutuhan | Lokasi |
|---|---|
| Root app dan plugin | `src/app` |
| Registrasi route | `src/app/router` |
| Page route-level | `src/features/<feature>/pages` |
| Component khusus domain | `src/features/<feature>/components` |
| Component UI generik | `src/shared/components/ui` |
| Navbar/sidebar generik | `src/shared/components/navigation` |
| API request domain | `src/features/<feature>/api` |
| HTTP client global | `src/shared/api` |
| State global fitur | `src/features/<feature>/stores` |
| Composition logic fitur | `src/features/<feature>/composables` |
| Composition logic generik | `src/shared/composables` |
| Type domain | `src/features/<feature>/types` |
| Type generik lintas fitur | `src/shared/types` |
| Validation domain | `src/features/<feature>/validation` |
| Utility domain | Tetap di fitur terkait |
| Utility generik | `src/shared/utils` |
| Layout halaman | `src/layouts` |
| Gambar yang di-import | `src/assets/images` |
| File statis root URL | `public` |
| Global stylesheet | `src/assets/styles/main.css` |

---

## 27. Instruksi Final untuk AI Agent

Sebelum membuat kode, selalu tanyakan secara internal:

1. Fitur ini milik domain apa?
2. Apakah file serupa sudah ada?
3. Apakah logic ini lokal, reusable dalam fitur, atau benar-benar shared?
4. Apakah state ini membutuhkan Pinia?
5. Apakah component ini dapat tetap presentasional?
6. Apakah API request sudah dipisahkan?
7. Apakah kontrak TypeScript sudah jelas?
8. Apakah perubahan ini menambah coupling antar-feature?
9. Apakah ada perubahan yang tidak diminta?
10. Apakah hasilnya lolos type-check dan build?

Jika agent tidak dapat menentukan lokasi file dengan yakin, agent harus memilih lokasi yang paling dekat dengan domain fitur, bukan langsung menaruhnya di `shared`.
