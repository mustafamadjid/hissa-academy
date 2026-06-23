# Feature: Manajemen Lesson oleh Administrator

Fitur ini digunakan oleh administrator untuk mengelola lesson pada suatu course, termasuk melihat daftar lesson, menambahkan lesson baru, melihat detail lesson, memperbarui data lesson, dan menghapus lesson.

## Daftar Endpoint

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/v1/admin/courses/{course_uuid}/lessons` | Menampilkan daftar lesson milik course berdasarkan urutan. |
| `POST` | `/api/v1/admin/courses/{course_uuid}/lessons` | Menambahkan lesson baru ke dalam course. |
| `GET` | `/api/v1/admin/lessons/{lesson_uuid}` | Menampilkan detail lesson beserta metadata video. |
| `PATCH` | `/api/v1/admin/lessons/{lesson_uuid}` | Mengubah data lesson seperti `title`, `position`, dan `is_required`. |
| `DELETE` | `/api/v1/admin/lessons/{lesson_uuid}` | Menghapus atau melakukan soft delete lesson sesuai dependency data. |

## Fitur yang Tersedia

### 1. Melihat Daftar Lesson

Administrator dapat melihat seluruh lesson yang terdaftar pada suatu course. Data lesson ditampilkan berdasarkan nilai `position` agar urutan guided learning tetap konsisten.

### 2. Menambahkan Lesson

Administrator dapat membuat lesson baru pada course tertentu. Lesson baru dapat memuat informasi seperti:

- Judul lesson
- ID video YouTube
- Posisi lesson
- Status wajib atau opsional

### 3. Melihat Detail Lesson

Administrator dapat melihat informasi lengkap suatu lesson berdasarkan `lesson_uuid`, termasuk data utama lesson dan metadata video yang terkait.

### 4. Memperbarui Lesson

Administrator dapat memperbarui sebagian data lesson menggunakan method `PATCH`. Data yang dapat diperbarui meliputi:

- `title`
- `position`
- `is_required`

Perubahan posisi harus mempertahankan urutan lesson dalam course dan menghindari nilai posisi yang duplikat.

### 5. Menghapus Lesson

Administrator dapat menghapus lesson berdasarkan `lesson_uuid`. Penghapusan dapat menggunakan mekanisme soft delete apabila lesson sudah memiliki dependency, seperti:

- Progress peserta
- Data kuis
- Riwayat aktivitas
- Data lain yang membutuhkan referensi lesson

Lesson yang belum memiliki dependency dapat dihapus sesuai kebijakan sistem.
