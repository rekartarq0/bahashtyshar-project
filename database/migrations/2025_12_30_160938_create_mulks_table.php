<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
{
    Schema::create('mulks', function (Blueprint $table) {
        $table->id();

        // user_id
        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        // type_project_id
        $table->foreignId('type_project_id')
              ->nullable()
              ->constrained('type_projects')
              ->nullOnDelete();

              // location_id
        $table->foreignId('location_id')
              ->nullable()
              ->constrained('locations')
              ->nullOnDelete();

        $table->string('name');
        $table->string('phone')->nullable();
        $table->text('location')->nullable();
        $table->double('price');

        $table->text('Rwbar')->nullable();
        $table->text('note')->nullable();
        $table->text('number_mulk')->nullable();
        $table->text('link_location')->nullable();

                    $table->boolean('is_archived')->default(false);


        $table->softDeletes();
        $table->timestamps();
    });
}

public function down(): void
    {
        Schema::dropIfExists('mulks');
    }
};
