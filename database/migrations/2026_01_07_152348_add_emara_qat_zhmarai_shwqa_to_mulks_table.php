<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mulks', function (Blueprint $table) {
            $table->string('emara')->nullable()->after('location_id');       // عيماره
            $table->string('qat')->nullable()->after('emara');               // قات
            $table->string('zhmarai_shwqa')->nullable()->after('qat');       // ژ. شوقە
        });
    }

    public function down(): void
    {
        Schema::table('mulks', function (Blueprint $table) {
            $table->dropColumn(['emara', 'qat', 'zhmarai_shwqa']);
        });
    }
};
