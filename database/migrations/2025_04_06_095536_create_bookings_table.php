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

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('salon_id');
            $table->foreign('salon_id')->references('id')->on('salons');
            $table->text('notes')->nullable();
            $table->text('salon_notes')->nullable();
            $table->enum('created_by', ['customer', 'provider']);
            $table->timestamps();
            $table->softDeletes();


            $table->enum('status', ["pending", "confirmed", "completed", "cancelled", "Rejected"]);
            $table->date('date');
            $table->time('time');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
