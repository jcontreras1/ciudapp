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
        Schema::create('subcategory_relationship', function (Blueprint $table) {
            $table->id();
            $table->integer('percentage')->nullable();
            $table->unsignedBigInteger('origin_id');
            $table->foreign('origin_id')->references('id')->on('subcategory');
            $table->unsignedBigInteger('destiny_id');
            $table->foreign('destiny_id')->references('id')->on('subcategory');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategory_relationship');
    }
};
