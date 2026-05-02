<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_normals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->string('molat_no')->nullable();

            $table->string('name_one')->nullable();
            $table->string('penas_one')->nullable();
            $table->string('phone_one')->nullable();
            $table->string('name_two')->nullable();
            $table->string('penas_two')->nullable();
            $table->string('phone_two')->nullable();

            $table->string('mulk_one')->nullable();
            $table->string('mulk_type')->nullable();
            $table->string('mulk_no')->nullable();
            $table->string('mulk_garak')->nullable();
            $table->string('mulk_metter')->nullable();
            $table->string('nrxi_froshtn')->nullable();
            $table->string('nrxi_peshaki')->nullable();
            $table->string('nrxi_mawa')->nullable();

            $table->string('peshaki_layaniyakam')->nullable();
            $table->string('nrxi_pashimani_one')->nullable();
            $table->string('nrxi_pashimani_two')->nullable();

            $table->date('datecholkrdn_year')->nullable();
            $table->date('from_datecholkrdn')->nullable();
            $table->date('to_datecholkrdn')->nullable();
            $table->string('krey_mangana')->nullable();

            $table->string('skay_panjara')->nullable();
            $table->string('pankay_asman')->nullable();
            $table->string('sngi_cheshtnga')->nullable();
            $table->string('dukalkesh')->nullable();
            $table->string('naw_kwlenar')->nullable();
            $table->string('dastshor')->nullable();
            $table->string('tankiaw')->nullable();

            $table->string('garak_one')->nullable();
            $table->string('garak_two')->nullable();
            $table->string('job_one')->nullable();
            $table->string('job_two')->nullable();
            $table->string('shahid_one')->nullable();
            $table->string('shahid_two')->nullable();

            $table->date('date_invoice')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_normals');
    }
};
