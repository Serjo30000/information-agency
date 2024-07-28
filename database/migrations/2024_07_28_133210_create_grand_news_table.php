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
        Schema::create('grand_news', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_publication_date')->nullable();
            $table->timestamp('end_publication_date')->nullable();
            $table->integer('priority')->default(0);
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grand_news');
    }
};
