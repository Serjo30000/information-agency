<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regions_and_peoples', function (Blueprint $table) {
            $table->string('path_to_image', 255)->nullable(false)->change();
            $table->dropUnique(['path_to_image']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regions_and_peoples', function (Blueprint $table) {
            $table->string('path_to_image')->nullable()->unique()->change();
        });
    }
};
