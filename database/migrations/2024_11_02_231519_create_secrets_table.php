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
        Schema::create('secrets', function (Blueprint $table) {
            $table->id(); // Primary Key
            
            // Secret Type
            $table->enum('secret_type', ['text', 'file']); 

            // Secret info
            $table->text('message'); 
            $table->text('original_filename')->nullable();
            $table->text('message_key');
            $table->string('short_message_key');
            $table->string('url_identifier')->unique(); 

            // Security
            $table->boolean('is_password_protected')->default(false); 
            $table->string('password_hash')->nullable();
            $table->string('message_iv');
            $table->string('message_tag');

            // Expiration settings
            $table->boolean('clicks_expiration')->default(false); 
            $table->integer('clicks_remaining')->nullable(); 
            $table->boolean('views_expiration')->default(false); 
            $table->integer('days_remaining')->nullable(); 

            // Optional settings
            $table->boolean('allow_manual_deletion')->default(false); 
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('keep_track')->default(false);
            $table->string('alias')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secrets');
    }
};
