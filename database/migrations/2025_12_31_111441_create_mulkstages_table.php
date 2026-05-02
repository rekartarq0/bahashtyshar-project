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
      Schema::create('mulkstages', function (Blueprint $table) {
    $table->id();

    // user (who handled the stage)
    $table->foreignId('user_id')
          ->nullable()
          ->constrained()
          ->nullOnDelete();

    // mulk
    $table->foreignId('mulk_id')
          ->nullable()
          ->constrained('mulks')
          ->nullOnDelete();

          
$table->foreignId('customer_id')
      ->nullable()
      ->constrained('customers')
      ->cascadeOnDelete();


    $table->enum('stage', [

        'request',
        'prepare',
        'show',
        'handling',
        'contract',

    ]);

    $table->dateTime('start_time');
    $table->dateTime('end_time')->nullable();
    $table->text('note')->nullable();
   $table->boolean('is_pricing_complete')
                ->default(false);
            $table->boolean('backed_from_pricing')->default(false);

                
    $table->softDeletes();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mulkstages');
    }
};
