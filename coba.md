Buatkan api service untuk
PATCH /api/v1/admin/quizzes/{quiz_uuid} Mengubah nama, status aktif, dan konfigurasi
quiz.
DELETE /api/v1/admin/quizzes/{quiz_uuid} Menghapus/menonaktifkan quiz sesuai
dependency.
GET /api/v1/admin/quizzes/{quiz_uuid}/questions Daftar pertanyaan dan opsi jawaban untuk
admin.
POST /api/v1/admin/quizzes/{quiz_uuid}/questions Menambahkan pertanyaan dan jawaban untuk quiz

Buat implementasi mengikuti be-rules.md dan buat unit test nya berdasarkan unit-test-rules.md


{
    questions :[
        {
            quizz_id : ,
            question : ,
            position : ,
            answers : [
                {
                    answer : ,
                    is_correct :,
                },
                {
                    // dan seterusnya
                }
            ]
        },
        {
            //dan seterusnya
        }
    ]
}