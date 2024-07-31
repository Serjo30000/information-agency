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
        Schema::table('people_contents', function (Blueprint $table) {
            // Удаляем внешние ключи
            $table->dropForeign(['regions_and_peoples_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people_contents', function (Blueprint $table) {
            // Восстанавливаем внешние ключи
            $table->foreign('regions_and_peoples_id')->references('id')->on('regions_and_peoples')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }
};
