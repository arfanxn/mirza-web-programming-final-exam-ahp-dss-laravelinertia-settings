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
        Schema::create('performance_scores', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('alternative_id');
            $table->ulid('criterion_id');
            $table->decimal('value', 12, 2);
            $table->timestamps();

            $table->foreign('alternative_id')->references('id')->on('alternatives')->onDelete('CASCADE');
            $table->foreign('criterion_id')->references('id')->on('criteria')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_scores');
    }
};
