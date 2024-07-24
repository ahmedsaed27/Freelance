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
            $table->foreignId('profiles_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();


            $table->string('job_name');
            $table->unsignedBigInteger('countries_id');
            $table->enum('section' , ['personal_status']);

            $table->enum('specialization' , ['civil_law']);

            $table->enum('job_type' , ['fullTime' , 'partTime' , 'freelance']);
            $table->enum('work_place' , ['office' , 'house' , 'flexible']);
            $table->longText('responsibilities');

            $table->enum('career_level' , ['boss'  , 'expert' , 'mid_level' , 'junior' , 'student']);

            $table->date('from');
            $table->date('to');
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
