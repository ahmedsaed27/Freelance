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
        Schema::create('profile_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profiles_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('qualification');
            $table->string('university');
            $table->enum('specialization' , ['appeal']);
            $table->unsignedBigInteger('countries_id');

            $table->longText('additional_information')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_education');
    }
};
