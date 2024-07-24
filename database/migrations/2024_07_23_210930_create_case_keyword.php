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
        Schema::create('case_keyword', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('keyword_id')->constrained('keywords' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_keyword');
    }
};
