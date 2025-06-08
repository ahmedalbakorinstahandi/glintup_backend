<?php

return [
    'welcome' => 'مرحبًا بك!',
    'goodbye' => 'وداعًا!',
    'error_occurred' => 'حدث خطأ ما.',
    'item_not_found' => 'العنصر غير موجود.',
    'item_deleted_successfully' => 'تم حذف العنصر بنجاح.',
    'failed_delete_item' => 'فشل في حذف العنصر.',
    'item_updated_successfully' => 'تم تحديث العنصر بنجاح.',
    'item_created_successfully' => 'تم إنشاء العنصر بنجاح.',
    'permission_error' => 'ليس لديك صلاحية.',
    'request_filter_status_error' => 'خطأ في حالة تصفية الطلب.',
    'service_request_already_hired' => 'تم توظيف مقدم الخدمة بالفعل لهذا الطلب.',
    'status_updated_successfully' => 'تم تحديث الحالة بنجاح.',
    'invalid_cancel_request' => 'طلب إلغاء غير صالح.',
    'invalid_request_status' => 'حالة الطلب غير صالحة.',
    'invalid_request' => 'طلب غير صالح.',
    'invalid_credentials' => 'بيانات اعتماد غير صالحة.',
    'user_not_found' => 'المستخدم غير موجود.',
    'user_already_exists' => 'المستخدم موجود بالفعل.',

    'account_created_verify_phone' => 'تم إنشاء الحساب بنجاح. يرجى التحقق من رقم هاتفك.',
    'phone_not_found' => 'رقم الهاتف غير موجود.',
    'account_verified_successfully' => 'تم التحقق من الحساب بنجاح.',
    'otp_sent_successfully' => 'تم إرسال رمز التحقق بنجاح إلى رقم هاتفك: :phone.',
    'password_reset_successfully' => 'تم إعادة تعيين كلمة المرور بنجاح.',
    'invalid_or_expired_verification_code' => 'رمز التحقق غير صالح أو منتهي الصلاحية.',
    'unauthorized' => 'غير مسجل الدخول.',
    'user_logged_out_successfully' => 'تم تسجيل الخروج بنجاح.',
    'user_logged_in_successfully' => 'تم تسجيل الدخول بنجاح.',

    'activation_code_message' => 'رمز التفعيل الخاص بك في GlintUp هو: :verifyCode. يرجى استخدام هذا الرمز لتفعيل حسابك. سينتهي صلاحية هذا الرمز خلال 10 دقائق.',
    'password_reset_code_message' => 'رمز إعادة تعيين كلمة المرور الخاص بك في GlintUp هو: :verifyCode. يرجى استخدام هذا الرمز لإعادة تعيين كلمة مرورك. سينتهي صلاحية هذا الرمز خلال 10 دقائق.',
    
    'phone' => [
        'invalid' => 'رقم الهاتف غير صالح.',
    ],
    
    
    'service' => [
        'item_created_successfully' => 'تم إنشاء الخدمة بنجاح',
        'item_updated_successfully' => 'تم تحديث الخدمة بنجاح',
        'item_deleted_successfully' => 'تم حذف الخدمة بنجاح',
        'failed_delete_item'        => 'فشل في حذف الخدمة',
        'item_not_found'            => 'الخدمة غير موجودة',
    ],


    'group' => [
        'item_created_successfully' => 'تم إنشاء المجموعة بنجاح',
        'item_updated_successfully' => 'تم تحديث المجموعة بنجاح',
        'item_deleted_successfully' => 'تم حذف المجموعة بنجاح',
        'failed_delete_item'        => 'فشل في حذف المجموعة',
        'item_not_found'            => 'المجموعة غير موجودة',
        'item_reordered_successfully' => 'تم إعادة ترتيب المجموعة بنجاح',
    ],

    'group_service' => [
        'item_created_successfully' => 'تم ربط الخدمة بالمجموعة بنجاح',
        'item_updated_successfully' => 'تم تحديث الربط بنجاح',
        'item_deleted_successfully' => 'تم حذف الربط بنجاح',
        'failed_delete_item'        => 'فشل في حذف الربط',
        'item_not_found'            => 'الربط غير موجود',
    ],

    'salon' => [
        'item_created_successfully' => 'تم إنشاء الصالون بنجاح',
        'item_updated_successfully' => 'تم تحديث الصالون بنجاح',
        'item_deleted_successfully' => 'تم حذف الصالون بنجاح',
        'failed_delete_item'        => 'فشل في حذف الصالون',
        'item_not_found'            => 'الصالون غير موجود',
    ],

    'booking' => [
        'item_created_successfully' => 'تم إنشاء الحجز بنجاح',
        'item_updated_successfully' => 'تم تحديث الحجز بنجاح',
        'item_deleted_successfully' => 'تم حذف الحجز بنجاح',
        'failed_delete_item'        => 'فشل في حذف الحجز',
        'item_not_found'            => 'الحجز غير موجود',
        'service_not_in_salon' => 'الخدمة غير موجودة في الصالون',
        'booking_details' => 'الحجز #:code - :salon',
        'cannot_reschedule_cancelled_booking' => 'لا يمكن إعادة جدولة حجز ملغي',
        'cannot_reschedule_completed_booking' => 'لا يمكن إعادة جدولة حجز مكتمل',
        'reschedule_successfully' => 'تم إعادة جدولة الحجز بنجاح',
        'booking_cancelled' => 'تم إلغاء الحجز #:code - :salon',
        'cancel_successfully' => 'تم إلغاء الحجز بنجاح',
        'already_cancelled_booking' => 'الحجز ملغي بالفعل',
        'cannot_cancel_completed_booking' => 'لا يمكن إلغاء حجز مكتمل',
        'cannot_update_cancelled_booking' => 'لا يمكن تحديث حجز ملغي',
        'cannot_update_completed_booking' => 'لا يمكن تحديث حجز مكتمل',
        'slot_not_available' => 'لا يوجد وقت متاح للخدمة :service_name في التاريخ :date والوقت :start_time',
        'service_not_found_or_cancelled' => 'الخدمة غير موجودة أو ملغية',
        'cannot_cancel_service' => 'لا يمكن إلغاء الخدمة',
        'service_cancelled_successfully' => 'تم إلغاء الخدمة بنجاح',

        'rescheduled_successfully' => 'تم إعادة جدولة الحجز بنجاح',
        'rescheduled_booking' => 'تم إعادة جدولة الحجز #:code - :salon',
        'cannot_reschedule_rejected_booking' => 'لا يمكن إعادة جدولة حجز مرفوض',
        'service_not_found_or_invalid' => 'الخدمة غير موجودة أو غير صالحة',
        'services_rescheduled_successfully' => 'تم إعادة جدولة الخدمات بنجاح',

    ],

    'user' => [
        'item_created_successfully' => 'تم إنشاء المستخدم بنجاح',
        'item_updated_successfully' => 'تم تحديث المستخدم بنجاح',
        'item_deleted_successfully' => 'تم حذف المستخدم بنجاح',
        'failed_delete_item'        => 'فشل في حذف المستخدم',
        'item_not_found'            => 'المستخدم غير موجود',
        'not_enough_balance' => 'ليس لديك رصيد كافٍ في المحفظة',
        'is_banned' => 'المستخدم محظور',
        'already_registered' => 'المستخدم مسجل بالفعل',
        'registered_but_not_verified' => 'المستخدم مسجل ولكن غير مفعل',
    ],

    'promotion_ad' => [
        'item_created_successfully' => 'تم إنشاء الإعلان الترويجي بنجاح',
        'item_updated_successfully' => 'تم تحديث الإعلان الترويجي بنجاح',
        'item_deleted_successfully' => 'تم حذف الإعلان الترويجي بنجاح',
        'failed_delete_item'        => 'فشل في حذف الإعلان الترويجي',
        'item_not_found'            => 'الإعلان الترويجي غير موجود',
        'request_post_ad_successfull' => 'تم طلب نشر الإعلان بنجاح',
        'ad_already_sent_to_review' => 'الإعلان تم إرساله للمراجعة بالفعل',
        'ad_sent_to_review_successfully' => 'تم إرسال الإعلان للمراجعة بنجاح',
    ],

    'working_hour' => [
        'item_created_successfully' => 'تم إنشاء وقت العمل بنجاح',
        'item_updated_successfully' => 'تم تحديث وقت العمل بنجاح',
        'item_deleted_successfully' => 'تم حذف وقت العمل بنجاح',
        'failed_delete_item'        => 'فشل في حذف وقت العمل',
        'item_not_found'            => 'وقت العمل غير موجود',
        'this_day_of_week_already_exists' => 'يوجد بالفعل وقت عمل لهذا اليوم من الأسبوع',
    ],

    'notification' => [
        'item_created_successfully' => 'تم إنشاء الإشعار بنجاح',
        'item_updated_successfully' => 'تم تحديث الإشعار بنجاح',
        'item_deleted_successfully' => 'تم حذف الإشعار بنجاح',
        'failed_delete_item'        => 'فشل في حذف الإشعار',
        'item_not_found'            => 'الإشعار غير موجود',
        'send_notification_successfully' => 'تم إرسال الإشعار بنجاح',
    ],


    'salon_holiday' => [
        'item_created_successfully' => 'تم إضافة عطلة للصالون بنجاح',
        'item_updated_successfully' => 'تم تحديث عطلة الصالون بنجاح',
        'item_deleted_successfully' => 'تم حذف عطلة الصالون بنجاح',
        'failed_delete_item'        => 'فشل في حذف عطلة الصالون',
        'item_not_found'            => 'عطلة الصالون غير موجودة',
    ],

    'social_media_site' => [
        'item_created_successfully' => 'تم إنشاء موقع التواصل بنجاح',
        'item_updated_successfully' => 'تم تحديث موقع التواصل بنجاح',
        'item_deleted_successfully' => 'تم حذف موقع التواصل بنجاح',
        'failed_delete_item'        => 'فشل في حذف موقع التواصل',
        'item_not_found'            => 'موقع التواصل غير موجود',
    ],


    'salon_customer' => [
        'item_created_successfully' => 'تم ربط العميل بالصالون بنجاح',
        'item_updated_successfully' => 'تم تحديث بيانات العميل داخل الصالون',
        'item_deleted_successfully' => 'تم حذف العميل من الصالون بنجاح',
        'failed_delete_item'        => 'فشل في حذف العميل من الصالون',
        'item_not_found'            => 'العميل غير موجود داخل الصالون',
    ],

    'salon_social_media_site' => [
        'item_created_successfully' => 'تم ربط موقع التواصل بنجاح',
        'item_updated_successfully' => 'تم تحديث رابط التواصل بنجاح',
        'item_deleted_successfully' => 'تم حذف رابط التواصل بنجاح',
        'failed_delete_item'        => 'فشل في حذف رابط التواصل',
        'item_not_found'            => 'رابط التواصل غير موجود',
    ],

    'review' => [
        'item_created_successfully' => 'تم إنشاء المراجعة بنجاح',
        'item_updated_successfully' => 'تم تحديث المراجعة بنجاح',
        'item_deleted_successfully' => 'تم حذف المراجعة بنجاح',
        'failed_delete_item'        => 'فشل في حذف المراجعة',
        'item_not_found'            => 'المراجعة غير موجودة',
        'replied_successfully' => 'تم الرد على المراجعة بنجاح',
        'reported_successfully' => 'تم الإبلاغ عن المراجعة بنجاح',
        'create_review_error' => 'لا يمكنك إنشاء مراجعة بدون حجز واحد مكتمل على الأقل في هذا الصالون، أو لديك مراجعة بالفعل',
    ],

    'ad_payment_description' => 'دفع للإعلان #:ad_id',

    'wallet_deposit' => 'إيداع مبلغ للمحفظة :amount',

    'coupon' => [
        'item_created_successfully' => 'تم إنشاء الكوبون بنجاح',
        'item_updated_successfully' => 'تم تحديث الكوبون بنجاح',
        'item_deleted_successfully' => 'تم حذف الكوبون بنجاح',
        'failed_delete_item'        => 'فشل في حذف الكوبون',
        'item_not_found'            => 'الكوبون غير موجود',
        'code_already_exists' => 'الكود :code موجود بالفعل',
        'item_checked_successfully' => 'تم التحقق من الكوبون بنجاح',
        'is_invalid' => 'الكوبون غير صالح',
    ],


    'wallet_transaction' => [
        'item_created_successfully' => 'تم إنشاء معاملة المحفظة بنجاح',
        'item_updated_successfully' => 'تم تحديث معاملة المحفظة بنجاح',
        'item_deleted_successfully' => 'تم حذف معاملة المحفظة بنجاح',
        'failed_delete_item'        => 'فشل في حذف معاملة المحفظة',
        'item_not_found'            => 'معاملة المحفظة غير موجودة',
    ],


    'gift_card' => [
        'item_created_successfully' => 'تم إنشاء بطاقة الهدية بنجاح',
        'item_updated_successfully' => 'تم تحديث بطاقة الهدية بنجاح',
        'item_deleted_successfully' => 'تم حذف بطاقة الهدية بنجاح',
        'failed_delete_item'        => 'فشل في حذف بطاقة الهدية',
        'item_not_found'            => 'بطاقة الهدية غير موجودة',
        'cannot_send_to_yourself' => 'لا يمكنك إرسال بطاقة هدية لنفسك',
        'transaction_details' => 'تمت إضافة تكلفة بطاقة الهدية رقم #:code بقيمة :amount :currency. التفاصيل: :details. نتمنى لك تجربة رائعة!',
        'not_your_gift_card' => 'هذه ليست بطاقة هدية لك',
        'item_already_used' => 'تم استخدام بطاقة الهدية بالفعل',
        'item_already_received' => 'تم استلام بطاقة الهدية بالفعل',
        'received_transaction_details' => 'تم استلام بطاقة الهدية رقم #:code بقيمة :amount :currency.',
        'item_received_successfully' => 'تم استلام بطاقة الهدية بنجاح',
        'sent_successfully' => 'تم إرسال بطاقة الهدية بنجاح',
    ],

    'gift_card_message' => "مرحبًا،\n\nلقد استلمت بطاقة هدية من :sender.\n\nالتفاصيل:\n:details\n\nرسالة المرسل:\n:note\n\nيرجى تحميل التطبيق من الرابط أدناه والتسجيل للحصول على البطاقة.\n\n:link",

    'gift_card_amount_details' => "قيمة بطاقة الهدية: :amount :currency",

    'gift_card_service_details' => "الخدمات المشمولة في بطاقة الهدية:\n:services",


    'setting' => [
        'item_created_successfully' => 'تم إنشاء الإعداد بنجاح',
        'item_updated_successfully' => 'تم تحديث الإعداد بنجاح',
        'item_deleted_successfully' => 'تم حذف الإعداد بنجاح',
        'failed_delete_item'        => 'فشل في حذف الإعداد',
        'item_not_found'            => 'الإعداد غير موجود',
    ],

    'salon_staff' => [
        'item_created_successfully'       => 'تم إضافة عضو جديد إلى الطاقم',
        'item_updated_successfully'       => 'تم تحديث بيانات عضو الطاقم',
        'item_deleted_successfully'       => 'تم حذف عضو الطاقم بنجاح',
        'failed_delete_item'              => 'فشل في حذف عضو الطاقم',
        'item_not_found'                  => 'عضو الطاقم غير موجود',
        'permissions_updated_successfully' => 'تم تحديث صلاحيات عضو الطاقم بنجاح',
    ],

    'salon_payment' => [
        'item_created_successfully' => 'تم إنشاء دفعة الصالون بنجاح',
        'item_updated_successfully' => 'تم تحديث دفعة الصالون بنجاح',
        'item_deleted_successfully' => 'تم حذف دفعة الصالون بنجاح',
        'failed_delete_item'        => 'فشل في حذف دفعة الصالون',
        'item_not_found'            => 'دفعة الصالون غير موجودة',
    ],


    'loyalty_point' => [
        'item_created_successfully' => 'تم إنشاء نقاط الولاء بنجاح',
        'item_updated_successfully' => 'تم تحديث نقاط الولاء بنجاح',
        'item_deleted_successfully' => 'تم حذف نقاط الولاء بنجاح',
        'failed_delete_item'        => 'فشل في حذف نقاط الولاء',
        'item_not_found'            => 'نقاط الولاء غير موجودة',
        'already_received' => 'تم استلام نقاط الولاء بالفعل',
        'salon_deos_not_have_loyalty_service' => 'الصالون لم يحدد خدمة ولاء',
        'item_received_successfully' => 'تم استلام نقاط الولاء بنجاح',
        'not_enough_points' => 'يجب أن يحتوي الكوبون على 5 طوابع',
    ],

    'phone_already_exists' => 'رقم الهاتف موجود بالفعل',
    'salon_registered_successfully' => 'تم تسجيل الصالون بنجاح',
    'phone_already_taken' => 'رقم الهاتف مستخدم بالفعل',

    'complaint' => [
        'item_created_successfully' => 'تم إرسال الشكوى بنجاح',
        'item_updated_successfully' => 'تم تحديث حالة الشكوى',
        'item_deleted_successfully' => 'تم حذف الشكوى بنجاح',
        'failed_delete_item'        => 'فشل في حذف الشكوى',
        'item_not_found'            => 'الشكوى غير موجودة',
    ],

    'salon_menu_request' => [
        'created' => 'تم إنشاء طلب تعبئة المنيو بنجاح.',
        'updated' => 'تم تحديث طلب تعبئة المنيو بنجاح.',
        'deleted' => 'تم حذف طلب تعبئة المنيو بنجاح.',
        'not_found' => 'طلب تعبئة المنيو غير موجود.',
    ],

    'menu_request_payment_description' => 'دفع لطلب تعبئة المنيو',


    'admin_users' => [
        'item_created_successfully' => 'تم إنشاء المستخدم الإداري بنجاح.',
        'item_updated_successfully' => 'تم تعديل بيانات المستخدم الإداري بنجاح.',
        'item_deleted_successfully' => 'تم حذف المستخدم الإداري بنجاح.',
        'failed_delete_item' => 'فشل في حذف المستخدم الإداري.',
        'permissions_updated_successfully' => 'تم تحديث صلاحيات المستخدم الإداري بنجاح.',
        'item_not_found' => 'المستخدم الإداري غير موجود.',
    ],


    'contact' => [
        'item_created_successfully' => 'تم إنشاء جهة الاتصال بنجاح',
        'item_updated_successfully' => 'تم تحديث جهة الاتصال بنجاح',
        'item_deleted_successfully' => 'تم حذف جهة الاتصال بنجاح',
        'failed_delete_item'        => 'فشل في حذف جهة الاتصال',
        'item_not_found'            => 'جهة الاتصال غير موجودة',
    ],



];
