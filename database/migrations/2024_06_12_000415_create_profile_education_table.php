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
            $table->foreignId('profile_id')->constrained('profiles' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('major');
            $table->string('grade');
            $table->string('degree');
            $table->string('qualification')->nullable();
            $table->string('university');
            $table->unsignedBigInteger('country_id');
            $table->longText('additional_information')->nullable();
            $table->date('start_date');
            $table->date('end_date');
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
