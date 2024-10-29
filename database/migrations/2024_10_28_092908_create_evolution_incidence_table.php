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
        Schema::create('evolution_incidence', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incidence_id');
            $table->foreign('incidence_id')->references('id')->on('incidence');
            $table->unsignedBigInteger('incidence_status_id');
            $table->foreign('incidence_status_id')->references('id')->on('incidence_status');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evolution_incidence');
    }
};
