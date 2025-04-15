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

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('salon_id');
            $table->foreign('salon_id')->references('id')->on('salons');
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->text('salon_reply')->nullable();
            $table->timestamp('salon_reply_at')->nullable();
            $table->text('salon_report')->nullable();
            $table->enum('reason_for_report', ['inappropriate_content', 'spam', 'fake_review', 'other'])->nullable();
            $table->timestamp('salon_reported_at')->nullable();
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
        Schema::dropIfExists('reviews');
    }
};
