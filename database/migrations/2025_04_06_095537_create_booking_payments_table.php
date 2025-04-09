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

        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 5)->default('AED');
            $table->enum('type', ["deposit", "full", "refund"]);
            $table->enum('method', ["wallet"]);
            $table->enum('status', ["pending", "confirm", "canceled", "rejected"]);
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
        Schema::dropIfExists('booking_payments');
    }
};
