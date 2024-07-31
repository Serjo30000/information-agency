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
        Schema::table('grand_news', function (Blueprint $table) {
            // Remove nullable and set default value to current timestamp
            $table->timestamp('start_publication_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
            $table->timestamp('end_publication_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grand_news', function (Blueprint $table) {
            // Revert the changes made in the up method
            $table->timestamp('start_publication_date')->nullable()->default(null)->change();
            $table->timestamp('end_publication_date')->nullable()->default(null)->change();
        });
    }
};
