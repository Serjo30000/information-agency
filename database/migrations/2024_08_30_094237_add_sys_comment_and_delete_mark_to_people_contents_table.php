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
        Schema::table('people_contents', function (Blueprint $table) {
            $table->string('sys_Comment', 3000)->nullable()->after('publication_date');
            $table->boolean('delete_mark')->default(false)->after('sys_Comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people_contents', function (Blueprint $table) {
            $table->dropColumn('sys_Comment');
            $table->dropColumn('delete_mark');
        });
    }
};
