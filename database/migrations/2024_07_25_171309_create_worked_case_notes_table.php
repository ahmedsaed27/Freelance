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
        Schema::create('worked_case_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worked_case_id')->constrained('worked_cases' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('created_by_user_id');            $table->longText('content');
            $table->foreignId('parent_id')->nullable()->constrained('worked_case_notes' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worked_case_notes');
    }
};
