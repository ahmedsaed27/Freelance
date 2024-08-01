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
        Schema::create('profile_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('company');

            $table->string('job_title');
            $table->unsignedBigInteger('country_id');
     
            $table->enum('job_type' , ['fullTime' , 'partTime' , 'freelance']);
            $table->enum('work_place' , ['office' , 'house' , 'flexible']);
            $table->longText('responsibilities');

            $table->enum('career_level' , ['boss'  , 'expert' , 'mid_level' , 'junior' , 'student']);

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
        Schema::dropIfExists('profile_work_experiences');
    }
};
