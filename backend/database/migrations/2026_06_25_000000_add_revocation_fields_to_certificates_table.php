<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table): void {
            $table->text('revoked_reason')->nullable()->after('pdf_path');
            $table->timestamp('revoked_at')->nullable()->after('revoked_reason');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table): void {
            $table->dropColumn(['revoked_reason', 'revoked_at']);
        });
    }
};
