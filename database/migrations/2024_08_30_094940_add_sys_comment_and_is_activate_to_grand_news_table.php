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
        Schema::table('grand_news', function (Blueprint $table) {
            $table->string('sys_Comment', 3000)->nullable()->after('priority');
            $table->boolean('isActivate')->default(true)->after('sys_Comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grand_news', function (Blueprint $table) {
            $table->dropColumn('sys_Comment');
            $table->dropColumn('isActivate');
        });
    }
};
