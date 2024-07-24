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
        Schema::create('profile_paper', function (Blueprint $table) {
            $table->id();
            
            $table->bigInteger('profiles_id')->unsigned();
            $table->foreign('profiles_id')->references('id')->on('profiles')->cascadeOnDelete()->cascadeOnUpdate();

            $table->bigInteger('papers_id')->unsigned();
            $table->foreign('papers_id')->references('id')->on('papers')->cascadeOnDelete()->cascadeOnUpdate();

            $table->text('value');
            $table->enum('status', ['Under Review', 'Approved', 'Rejected'])->default('Under Review');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_paper');
    }
};
