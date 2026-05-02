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
        Schema::create('invoices_asaishes', function (Blueprint $table) {
            $table->id();
            
    $table->unsignedBigInteger('user_id')->nullable();
    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

    $table->unsignedInteger('invoice_no');   // 1, 2, 3 ...
    $table->unsignedSmallInteger('invoice_year'); // 2025, 2026

    
    $table->string('to'); // bo  barez barewabari gshty
    $table->string('matter'); // babat
    $table->text('content'); // nawarok agar habi
    $table->string('name_one')->nullable();
    $table->string('name_two')->nullable();
    $table->string('national_one')->nullable();
    $table->string('national_two')->nullable();
    $table->string('phone_one')->nullable();
    $table->string('phone_two')->nullable();
    $table->string('code_one')->nullable();
    $table->string('code_two')->nullable();

    $table->string('mamala_type')->nullable();

    $table->string('zhmarai_mulk')->nullable();
    $table->string('zhmarai_xanw')->nullable();
    $table->string('zhmarai_garak')->nullable();
    $table->string('zhmarai_kolan')->nullable();
    $table->text('note')->nullable();


    $table->softDeletes();
    $table->timestamps();

    // prevent duplicates per year
    $table->unique(['invoice_no', 'invoice_year']);   
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_asaishes');
    }
};
