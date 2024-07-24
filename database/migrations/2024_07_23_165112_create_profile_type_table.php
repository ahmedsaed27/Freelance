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
        Schema::create('profile_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('type_id')->constrained('types' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_type');
    }
};
