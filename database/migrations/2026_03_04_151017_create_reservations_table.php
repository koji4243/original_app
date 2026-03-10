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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('nhk_title');
            $table->string('nhk_description');
            $table->string('nhk_genres');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('nhk_tvEpisodeId');
            $table->string('nhk_code');
            $table->boolean('is_active');
            $table->dateTime('notify_at')->default(null);
            $table->integer('notify_before_min');
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
