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
        Schema::table('lesson_videos', function (Blueprint $table) {
            $table->string('video_id')->nullable()->after('video_url');
            $table->string('title')->nullable()->after('video_id');
            $table->text('description')->nullable()->after('title');
            $table->string('channel_title')->nullable()->after('description');
            $table->string('thumbnail_url')->nullable()->after('channel_title');
            $table->string('duration_iso')->nullable()->after('duration_seconds');
            $table->string('privacy_status')->nullable()->after('duration_iso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_videos', function (Blueprint $table) {
            $table->dropColumn([
                'video_id',
                'title',
                'description',
                'channel_title',
                'thumbnail_url',
                'duration_iso',
                'privacy_status',
            ]);
        });
    }
};
