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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('activityable_type', 255)->nullable()->after('description');
            $table->unsignedBigInteger('activityable_id')->nullable()->after('activityable_type');
            $table->string('refrence', 255)->nullable()->after('activityable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('activityable_type');
            $table->dropColumn('activityable_id');
        });
    }
};
