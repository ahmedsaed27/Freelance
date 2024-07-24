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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->boolean('is_visible')->comment('0 => false , 1 => true')->default(true);
            $table->foreignId('type_id')->constrained('types' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->enum('specialization' , ['civil_law'])->default('civil_law');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');
            $table->foreignId('currency_id')->constrained('currencies' , 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->float('min_amount');
            $table->float('max_amount');
            $table->longText('description');
            $table->enum('status' , ['Opened' , 'Assigned'])->default('Opened');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
