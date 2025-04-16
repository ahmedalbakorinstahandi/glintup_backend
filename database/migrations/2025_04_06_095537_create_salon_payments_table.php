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

        Schema::create('salon_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('salon_id');
            $table->foreign('salon_id')->references('id')->on('salons');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 5)->default('AED');
            $table->enum('method', ["wallet", "stripe", "cash"]);
            $table->enum('status', ["pending", "confirm", "canceled", "rejected"]);
            $table->boolean('is_refund')->default(false);
            $table->float('system_percentage', 10, 2)->default(0);
            $table->morphs('paymentable');
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
        Schema::dropIfExists('salon_payments');
    }
};
