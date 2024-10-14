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
        Schema::create('region_subcategory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->foreign('region_id')->references('id')->on('region');
            $table->foreign('subcategory_id')->references('id')->on('subcategory');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_subcategory');
    }
};
