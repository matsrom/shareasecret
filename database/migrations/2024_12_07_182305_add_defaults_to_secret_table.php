<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero actualizar los valores NULL existentes
        DB::table('secrets')->whereNull('clicks_remaining')->update(['clicks_remaining' => 0]);
        DB::table('secrets')->whereNull('days_remaining')->update(['days_remaining' => 0]);

        // Luego modificar la estructura de la tabla
        Schema::table('secrets', function (Blueprint $table) {
            $table->integer('clicks_remaining')->default(0)->change();
            $table->integer('days_remaining')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('secrets', function (Blueprint $table) {
            $table->integer('clicks_remaining')->change(0);
            $table->integer('days_remaining')->change(0);
        });
    }
};
