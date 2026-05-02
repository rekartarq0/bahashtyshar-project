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
        Schema::create('mulk_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mulk_id');
            $table->string('path'); // store image path
            $table->foreign('mulk_id')
                ->references('id')->on('mulks')
                ->onDelete('cascade');
            $table->timestamps();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mulk_images');
    }
};
