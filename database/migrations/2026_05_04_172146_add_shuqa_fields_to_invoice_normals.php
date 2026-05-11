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
        Schema::table('invoice_normals', function (Blueprint $table) {
        $table->string('emara')->nullable();
        $table->string('qat')->nullable();
        $table->string('zhmarai_shwqa')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    { 
        Schema::table('invoice_normals', function (Blueprint $table) {
        $table->dropColumn(['emara', 'qat', 'zhmarai_shwqa']);
    });
    }
};
