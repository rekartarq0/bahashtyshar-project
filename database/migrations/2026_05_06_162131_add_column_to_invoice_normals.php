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
            $table->string('phone_one_shahid')->nullable();    
            $table->string('phone_two_shahid')->nullable();
            $table->string('zhmarai_rozhi_cholkrdn')->nullable();
                    $table->string('krey_mangana_currency')->default('IQD')->after('krey_mangana');
                    //invoice add mawai katy choLkrdn
                    $table->string('mawai_katy_cholkrdn')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_normals', function (Blueprint $table) {
            $table->dropColumn(['phone_one_shahid', 'phone_two_shahid','zhmarai_rozhi_cholkrdn','krey_mangana_currency','mawai_katy_cholkrdn']);
        });
    }
};
