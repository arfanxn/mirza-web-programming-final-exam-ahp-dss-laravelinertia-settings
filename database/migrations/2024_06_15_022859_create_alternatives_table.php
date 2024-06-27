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
        Schema::create('alternatives', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('goal_id');;
            $table->string('name', 128);
            $table->unsignedTinyInteger('index');
            $table->timestamps();

            $table->foreign('goal_id')->references('id')->on('goals')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternatives');
    }
};
