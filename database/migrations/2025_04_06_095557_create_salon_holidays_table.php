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
        Schema::disableForeignKeyConstraints();

        Schema::create('salon_holidays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salon_id');
            $table->foreign('salon_id')->references('id')->on('salons');
            $table->date('holiday_date');
            $table->string('reason');
            $table->boolean('is_full_day');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salon_holidays');
    }
};
