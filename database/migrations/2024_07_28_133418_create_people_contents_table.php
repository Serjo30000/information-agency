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
        Schema::create('people_contents', function (Blueprint $table) {
            $table->id();
            $table->string('path_to_image')->nullable();
            $table->string('title');
            $table->text('content');
            $table->string('source')->nullable();
            $table->string('type');
            $table->timestamp('publication_date')->nullable();
            $table->foreignId('regions_and_peoples_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_contents');
    }
};
