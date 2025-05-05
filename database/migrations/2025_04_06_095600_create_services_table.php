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

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salon_id');
            $table->foreign('salon_id')->references('id')->on('salons');
            $table->text('name');
            $table->text('description');
            $table->string('icon', 110);
            $table->integer('duration_minutes');
            $table->decimal('price', 10, 2);
            $table->string('currency');
            $table->decimal('discount_percentage', 5, 2)->nullable()->default(0);
            $table->enum('gender', ["male","female","both"]);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_home_service')->default(false);
            $table->boolean('is_beautician')->default(false);
            $table->integer('capacity')->default(1);
            $table->unsignedBigInteger('order');
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
        Schema::dropIfExists('services');
    }
};
