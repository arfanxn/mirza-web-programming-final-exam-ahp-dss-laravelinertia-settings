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
        Schema::create('pairwise_comparisons', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('primary_criterion_id');
            $table->ulid('secondary_criterion_id');
            $table->decimal('value', 12, 2);
            $table->timestamps();

            $table->foreign('primary_criterion_id')->references('id')->on('criteria')->onDelete('CASCADE');
            $table->foreign('secondary_criterion_id')->references('id')->on('criteria')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pairwise_comparisons');
    }
};
