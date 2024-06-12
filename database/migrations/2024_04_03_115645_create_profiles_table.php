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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->tinyInteger('type')->comment('1 => Translators , 2 => Accountant , 2 => Lawyer');
            $table->longText('location');
            $table->json('areas_of_expertise');
            $table->integer('hourly_rate');
            $table->tinyInteger('years_of_experience');
            $table->string('career');

            /****************************** New Data ******************************/
            $table->unsignedBigInteger('countries_id');
            $table->unsignedBigInteger('cities_id');
            $table->enum('field' , ['appeal']);
            $table->enum('specialization' , ['appeal']);
            $table->enum('experience' , ['boss'  , 'expert' , 'mid_level' , 'junior' , 'student']);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
