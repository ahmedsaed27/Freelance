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
        Schema::create('profile_socials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profile_id')->constrained('profiles' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            /** profile social media */
            // $table->string('instagram')->nullable();
            // $table->string('linkedin')->nullable();
            // $table->string('facebook')->nullable();

            $table->foreignId('social_id')->constrained('social_media' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('link');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_socials');
    }
};
