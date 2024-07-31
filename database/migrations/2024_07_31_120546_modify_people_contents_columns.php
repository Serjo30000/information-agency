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
            // Изменяем столбцы
            $table->string('type', 20)->change();
            $table->timestamp('publication_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
            $table->foreignId('regions_and_peoples_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people_contents', function (Blueprint $table) {
            // Восстанавливаем исходные состояния столбцов
            $table->string('type')->change();
            $table->timestamp('publication_date')->nullable()->change();
            $table->foreignId('regions_and_peoples_id')->nullable()->change();
        });
    }
};
