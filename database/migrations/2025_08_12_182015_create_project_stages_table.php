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
        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete(); // Will set project_id to NULL if project is deleted

            $table->enum('stage', ['Planning', 'Creative', 'Design', 'Sale']);

            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();


            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_stages');
    }
};
