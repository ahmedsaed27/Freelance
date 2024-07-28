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
        Schema::create('case_profile_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_profile_id')->constrained('case_profile' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('created_by_user_id');

            $table->longText('content');
            $table->foreignId('parent_id')->nullable()->constrained('case_profile_notes' , 'id')->cascadeOnDelete()->cascadeOnUpdate();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_profile_notes');
    }
};
