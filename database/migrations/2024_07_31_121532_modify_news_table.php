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
        Schema::table('news', function (Blueprint $table) {
            // Убираем nullable и добавляем уникальность для 'path_to_image_or_video'
            $table->string('path_to_image_or_video')->unique()->nullable(false)->change();

            // Убираем nullable и устанавливаем текущее время по умолчанию для 'publication_date'
            $table->timestamp('publication_date')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false)->change();

            // Добавляем новый столбец 'regions_and_peoples_id' с внешним ключом
            $table->foreignId('regions_and_peoples_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Восстанавливаем исходные состояния столбцов
            $table->string('path_to_image_or_video')->nullable()->unique(false)->change();
            $table->timestamp('publication_date')->nullable()->default(null)->change();

            // Удаляем внешний ключ и столбец 'regions_and_peoples_id'
            $table->dropForeign(['regions_and_peoples_id']);
            $table->dropColumn('regions_and_peoples_id');
        });
    }
};
