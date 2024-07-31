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
        Schema::table('regions_and_peoples', function (Blueprint $table) {
            // Modify columns according to requirements
            $table->string('path_to_image')->nullable(false)->change();
            $table->string('position_or_type_region', 100)->change();
            $table->string('fio_or_name_region', 120)->change();
            $table->string('place_work', 255)->nullable(true)->change();
            $table->text('content')->change();
            $table->string('type', 10)->change();
            $table->date('date_birth_or_date_foundation')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions_and_peoples', function (Blueprint $table) {
            // Reverse the changes made in the up method
            $table->string('path_to_image')->nullable()->change();
            $table->string('position_or_type_region')->change();
            $table->string('fio_or_name_region')->change();
            $table->string('place_work')->nullable()->change();
            $table->text('content')->nullable()->change();
            $table->string('type')->change();
            $table->date('date_birth_or_date_foundation')->nullable()->change();
        });
    }
};
