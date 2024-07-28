<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('regions_and_peoples', function (Blueprint $table) {
            $table->id();
            $table->string('path_to_image')->nullable();
            $table->string('position_or_type_region');
            $table->string('fio_or_name_region');
            $table->string('place_work')->nullable();
            $table->text('content')->nullable();
            $table->string('type');
            $table->date('date_birth_or_date_foundation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions_and_peoples');
    }
};
