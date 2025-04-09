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


        // Schema::create('salons', function (Blueprint $table) {
        //     $table->id();
        
        //     // علاقات
        //     $table->unsignedBigInteger('owner_id')->nullable();
        //     $table->foreign('owner_id')->references('id')->on('users');
        
        //     // بيانات التعريف
        //     $table->string('company_legal_name')->nullable();      // الاسم القانوني للشركة
        //     $table->string('commercial_name')->nullable();          // الاسم التجاري
        //     $table->string('name', 255);                            // الاسم المختصر أو الاسم الظاهر (مطلوب)
        //     $table->string('icon', 110);
        
        //     // العناوين
        //     $table->string('branch_address')->nullable();           // عنوان المحل (الفرع الأول)
        //     $table->string('emirate')->nullable();                  // الإمارة
        //     $table->string('street')->nullable();                   // الشارع
        //     $table->string('location', 255);                        // الموقع العام (نص)
        
        //     // بيانات الاتصال العامة
        //     $table->string('phone_code', 7);
        //     $table->string('phone', 12);
        //     $table->string('email')->nullable();
        
        //     // جهة الاتصال
        //     $table->string('contact_name')->nullable();
        //     $table->string('contact_phone')->nullable();
        //     $table->string('contact_email')->nullable();
        
        //     // بيانات تجارية
        //     $table->string('commercial_phone')->nullable();
        //     $table->string('commercial_email')->nullable();
        
        //     // معلومات إدارية
        //     $table->text('description')->nullable();
        //     $table->boolean('is_approved')->default(false);
        //     $table->boolean('is_active')->default(true);
        //     $table->enum('type', ["salon", "home_service", "beautician", "clinic"]);
        
        //     // الموقع الجغرافي
        //     $table->double('latitude', 15, 8)->nullable();
        //     $table->double('longitude', 15, 8)->nullable();
        //     $table->string('country');
        //     $table->string('city');
        
        //     // تاريخ التسجيل
        //     $table->date('registration_date')->nullable();
        
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salons');
    }
};
