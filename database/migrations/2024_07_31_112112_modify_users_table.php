<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop unique constraint from 'phone' column
            $table->dropUnique('users_phone_unique');

            // Add unique constraint to 'login' column
            $table->unique('login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverse the changes made in the up() method

            // Drop unique constraint from 'login' column
            $table->dropUnique('users_login_unique');

            // Add unique constraint to 'phone' column
            $table->unique('phone');
        });
    }
};
