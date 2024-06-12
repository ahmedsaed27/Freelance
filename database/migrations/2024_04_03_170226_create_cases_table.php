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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->boolean('is_visible')->comment('0 => false , 1 => true');
            $table->string('freelance_type')->comment('its meen profile_type');
            $table->string('title');
            $table->enum('specialization' , ['civil_law']);
            $table->unsignedBigInteger('countries_id');
            $table->unsignedBigInteger('cities_id');
            $table->unsignedBigInteger('proposed_budget');
            $table->string('currency');
            $table->longText('keywords');
            $table->longText('notes');
            $table->longText('required_skills')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
