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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->enum('status', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('hours');
            $table->boolean('is_paid')->default(false);
            $table->foreignId('profile_id')->constrained('profiles', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('user_id')->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
