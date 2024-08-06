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
        Schema::create('worked_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('case_id')->constrained('cases' , 'id')->cascadeOnDelete()->cascadeOnUpdate();

            $table->float('rate');
            $table->foreignId('currency_id')->constrained('currencies' , 'id')->cascadeOnDelete()->cascadeOnUpdate();

            $table->enum('status' , ['Pending' , 'In Progress' , 'Completed'])->default('Pending');

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_paid')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worked_cases');
    }
};
