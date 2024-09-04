<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people_contents', function (Blueprint $table) {
            // Вернуть поле к предыдущему типу
            $table->timestamp('publication_date')->nullable()->change();
        });
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people_contents', function (Blueprint $table) {
            // Изменить тип поля на date
            $table->timestamp('publication_date')->nullable()->change();
        });
    }
};
