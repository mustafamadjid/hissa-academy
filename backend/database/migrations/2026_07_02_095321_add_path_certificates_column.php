<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('certificates', 'path') && ! Schema::hasColumn('certificates', 'pdf_path')) {
            Schema::table('certificates', function (Blueprint $table): void {
                $table->renameColumn('path', 'pdf_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The canonical pdf_path column is created by the certificates table migration.
    }
};
