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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); //naw
            $table->text('address')->nullable(); //nawnishan
            $table->string('phone1')->nullable(); //zhmarai talaphone 1
            $table->string('phone2')->nullable(); //zhmarai talaphone 2
            $table->text('title1')->nullable(); //sarder 1
            $table->text('title2')->nullable(); //sarder 2
            $table->text('note')->nullable(); //tebeni
            $table->text('image')->nullable(); //tebeni
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
