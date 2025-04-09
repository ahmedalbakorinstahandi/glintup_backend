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

        Schema::create('salons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->foreign('owner_id')->references('id')->on('users');
            $table->string('name', 255);
            $table->string('icon', 110);
            $table->string('phone_code', 7);
            $table->string('phone', 12);
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->string('location', 255);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('type', ["salon", "home_service", "beautician", "clinic"]);
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude', 15, 8)->nullable();
            $table->string('country');
            $table->string('city');
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
        Schema::dropIfExists('salons');
    }
};
