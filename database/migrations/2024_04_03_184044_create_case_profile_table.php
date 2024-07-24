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
        Schema::create('case_profile', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profile_id')->constrained('profiles' , 'id')->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('case_id')->constrained('cases' , 'id')->cascadeOnDelete()->cascadeOnUpdate();

            $table->float('suggested_rate');

            $table->longText('description');

            $table->enum('status' , ['Pending' , 'Accepted' , 'Rejected'])->default('Pending');

            $table->date('estimation_time');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_profile');
    }
};
