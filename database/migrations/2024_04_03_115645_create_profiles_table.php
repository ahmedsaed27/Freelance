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
            $table->longText('address');
            $table->json('areas_of_expertise');
            $table->integer('hourly_rate');
            $table->foreignId('currency_id')->constrained('currencies' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('years_of_experience');
            $table->string('career');

            /****************************** New Data ******************************/
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');
            $table->enum('field' , ['appeal'])->default('appeal');
            $table->enum('specialization' , ['appeal'])->default('appeal');
            $table->enum('level' , ['boss'  , 'expert' , 'mid_level' , 'junior' , 'student'])->default('mid_level');

            $table->softDeletes();
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
