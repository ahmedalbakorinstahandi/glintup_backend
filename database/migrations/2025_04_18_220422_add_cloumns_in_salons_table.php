<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            // ✏️ الحقول الجديدة / المعاد ترتيبها
            $table->string('merchant_legal_name')->after('owner_id');
            $table->string('merchant_commercial_name', 255)->after('merchant_legal_name');
            $table->string('address', 255)->after('merchant_commercial_name');
            $table->string('city_street_name')->after('address');
            $table->string('contact_name')->after('city_street_name');
            $table->string('contact_number')->after('contact_name');
            $table->string('contact_email')->after('contact_number');
            $table->string('business_contact_name')->after('contact_email');
            $table->string('business_contact_email')->after('business_contact_name');
            $table->string('business_contact_number')->after('business_contact_email');
            $table->string('types')->after('icon');
            $table->bigInteger('block_message')->nullable()->after('is_active');
            $table->text('bio')->nullable()->after('block_message');
            $table->text('tags')->nullable()->after('bio');

            // 📌 الحقول القديمة غير المستخدمة - نجمعها آخر الجدول
            $table->string('name', 255)->nullable()->after('tags')->change();
            $table->string('phone_code', 7)->nullable()->after('name')->change();
            $table->string('email')->nullable()->after('phone_code')->change();
            $table->string('location', 255)->nullable()->after('email')->change();
            $table->enum('type', ["salon", "home_service", "beautician", "clinic"])->nullable()->after('location')->change();
            $table->string('country')->nullable()->after('type')->change();
            $table->string('city')->nullable()->after('country')->change();
        });
    }

    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn([
                'merchant_legal_name',
                'merchant_commercial_name',
                'address',
                'city_street_name',
                'contact_name',
                'contact_number',
                'contact_email',
                'business_contact_name',
                'business_contact_email',
                'business_contact_number',
                'type',
                'block_message',
                'bio',
                'tags',

                // old fields (in case rollback)
                'name',
                'phone_code',
                'email',
                'location',
                'old_type',
                'country',
                'city',
            ]);
        });
    }
};
