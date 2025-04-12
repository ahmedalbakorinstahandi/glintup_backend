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

        Schema::create('promotion_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salon_id')->nullable();
            $table->foreign('salon_id')->references('id')->on('salons');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('image', 110)->nullable();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['in_review', 'approved', 'rejected'])->default('in_review');
            $table->unsignedBigInteger('views');
            $table->unsignedBigInteger('clicks');
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
        Schema::dropIfExists('promotion_ads');
    }
};
