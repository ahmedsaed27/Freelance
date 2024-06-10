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
            $table->bigInteger('user_id')->index();
            $table->boolean('is_visible')->comment('0 => false , 1 => true');
            $table->string('freelance_type')->comment('its meen profile_type');
            $table->string('title');
            $table->tinyInteger('country')->default(1)->comment('1 => egypt');
            $table->foreignId('cities_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->longText('notes');
            $table->longText('message')->nullable();
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
