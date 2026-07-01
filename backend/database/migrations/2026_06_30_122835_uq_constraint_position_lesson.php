<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropUnique('lessons_course_id_position_unique');
        });

        DB::statement(<<<'SQL'
            CREATE UNIQUE INDEX lessons_course_id_position_active_unique
            ON lessons (course_id, position)
            WHERE deleted_at IS NULL
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX lessons_course_id_position_active_unique');

        Schema::table('lessons', function (Blueprint $table) {
            $table->unique(
                ['course_id', 'position'],
                'lessons_course_id_position_unique'
            );
        });
    }
};
