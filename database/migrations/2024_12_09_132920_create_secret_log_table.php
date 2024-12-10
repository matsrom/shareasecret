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
        Schema::create('secret_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('secret_id');
            $table->string('ip_address');
            $table->string('browser');
            $table->string('os');
            $table->string('device');
            $table->string('country');
            $table->string('city');
            $table->timestamp('access_date');
            $table->boolean('is_successful');
            $table->timestamps();

            $table->foreign('secret_id')->references('id')->on('secrets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secret_logs');
    }
};
