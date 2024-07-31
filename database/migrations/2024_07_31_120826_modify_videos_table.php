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
        Schema::table('videos', function (Blueprint $table) {
            // Убираем nullable для 'source'
            $table->string('source')->nullable(false)->change();

            // Убираем nullable и устанавливаем текущее время по умолчанию для 'publication_date'
            $table->timestamp('publication_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            // Возвращаем nullable для 'source'
            $table->string('source')->nullable()->change();

            // Возвращаем nullable для 'publication_date'
            $table->timestamp('publication_date')->nullable()->default(null)->change();
        });
    }
};
