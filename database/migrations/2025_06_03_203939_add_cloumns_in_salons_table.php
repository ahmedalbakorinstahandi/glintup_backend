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
        Schema::table('salons', function (Blueprint $table) {
            // vat_number
            $table->string('vat_number')->nullable();
            $table->enum('service_location', ['in_house', 'in_center', 'in_house_and_center'])->nullable();

            // bank details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder_name')->nullable();
            $table->string('bank_account_iban')->nullable();

            // documents
            $table->string('services_list')->nullable()->comment('Services list document in Arabic and English');
            $table->string('trade_license')->nullable()->comment('Trade license document');
            $table->string('vat_certificate')->nullable()->comment('VAT certificate document');
            $table->string('bank_account_certificate')->nullable()->comment('Bank account certificate document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn('vat_number');
            $table->dropColumn('service_location');
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_account_number');
            $table->dropColumn('bank_account_holder_name');
            $table->dropColumn('bank_account_iban');
            $table->dropColumn('services_list');
            $table->dropColumn('trade_license');
            $table->dropColumn('vat_certificate');
            $table->dropColumn('bank_account_certificate');
        });
    }
};
