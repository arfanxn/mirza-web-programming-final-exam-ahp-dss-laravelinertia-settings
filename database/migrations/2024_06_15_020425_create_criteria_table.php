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
        Schema::create('criteria', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('goal_id');
            $table->string('name', 32);
            $table->boolean('impact_type');
            $table->unsignedTinyInteger('index')->nullable();
            $table->unsignedTinyInteger('weight')->nullable();
            $table->timestamps();

            $table->foreign('goal_id')->references('id')->on('goals')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};
