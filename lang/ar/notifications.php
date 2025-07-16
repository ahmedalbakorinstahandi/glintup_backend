<?php

return [
    'admin' => [
        'menu_request' => [
            'new_menu_request' => 'هنالك طلب تعبئة قائمة جديد',
            'new_menu_request_body' => 'قام المزود :salon_name بطلب تعبئة قائمة جديدة، رقم الطلب :menu_request_id, يرجى التحقق منه',
        ],
        'complaint' => [
            'new_complaint' => 'هنالك شكوى جديدة',
            'new_complaint_body' => 'قام المستخدم :full_user_name بإرسال شكوى، المحتوى: :content',
        ],
        'ad' => [
            'new_ad' => 'هنالك إعلان جديد',
            'new_ad_body' => 'قام المزود :salon_name بإضافة إعلان جديد، رقم الإعلان :promotion_ad_id, يرجى التحقق منه',
        ],
        'review' => [
            'new_review' => 'هنالك مراجعة جديدة',
            'new_review_body' => 'قام المستخدم :full_user_name بإضافة مراجعة جديدة، المحتوى: :review_content',
        ],
        'employee_permission' => [
            'update_employee_permission_title' => 'تم تحديث صلاحياتك',
            'update_employee_permission_body' => 'تم تحديث صلاحياتك بواسطة :admin_user_name',
        ],
        'user' => [
            'new_user' => 'هنالك مستخدم جديد',
            'new_user_body' => 'تم إنشاء مستخدم جديد :full_user_name, يرجى التحقق منه',
        ],
    ],

    'salon' => [
        'ad' => [
            'approve_ad' => 'تمت الموافقة على إعلانك',
            'approve_ad_body' => 'تمت الموافقة على إعلانك :ad_title, في المحل :salon_name',

            'reject_ad' => 'تم رفض إعلانك',
            'reject_ad_body' => 'تم رفض إعلانك :ad_title, في المحل :salon_name',
        ],
        'review' => [
            'new_review' => 'هنالك مراجعة جديدة',
            'new_review_body' => 'قام المستخدم :full_user_name بإضافة مراجعة جديدة، المحتوى: :review_content',
        ],
        'menu_request' => [
            'accept_title' => 'تم قبول طلب القائمة',
            'accept_body' => 'تم قبول طلب القائمة :menu_request_id, في المحل :salon_name',

            'reject_title' => 'تم رفض طلب القائمة',
            'reject_body' => 'تم رفض طلب القائمة :menu_request_id, في المحل :salon_name',
        ],
        'booking' => [
            'new_booking' => 'هنالك حجز جديد',
            'new_booking_body' => 'قام المستخدم :full_user_name بإنشاء حجز جديد في المحل :salon_name',
        ],
    ],

    'user' => [
        'gift_card' => [
            'new' => [
                'title' => 'لديك هدية جديدة',
                'body' => 'لقد أرسل لك :sender هدية جديدة',
            ],
            'receive' => [
                'title' => 'تم فتح هديتك',
                'body' => 'صديقك، :sender، فتح هديتك. شكرًا لك على نشر الفرحة ودعوته إلى Glint Up!',
            ],
        ],

        'booking' => [
            'completed' => [
                'title' => 'تم إكمال حجزك #:booking_id بنجاح',
                'body' => 'نود إعلامك بأنه تم إكمال حجزك في صالون :salon_name بنجاح. نأمل أن تكون قد حصلت على تجربة رائعة. نتطلع لرؤيتك مرة أخرى قريباً!',
            ],
            'cancelled' => [
                'title' => 'تم إلغاء حجزك #:booking_id في صالون :salon_name',
                'body' => 'نأسف لإبلاغك بأنه تم إلغاء حجزك في صالون :salon_name. إذا كان لديك أي استفسارات، يرجى التواصل مع خدمة العملاء. يمكنك دائماً إعادة الحجز في وقت آخر يناسبك.',
            ],
            'rejected' => [
                'title' => 'عذراً، تم رفض حجزك #:booking_id',
                'body' => 'نأسف لإبلاغك بأنه تم رفض حجزك في صالون :salon_name. قد يكون هذا بسبب جدول مواعيد مزدحم أو ظروف غير متوقعة. نرجو منك المحاولة مرة أخرى في وقت لاحق أو اختيار صالون آخر.',
            ],
            'new_booking' => [
                'title' => 'تم إنشاء حجز جديد',
                'body' => 'تم إنشاء حجز جديد في صالون :salon_name',
            ],
        ],
        'loyalty_point' => [
            'added_one_point' => [
                'title' => 'لقد حصلت على نقطة ولاء جديدة',
                'body' => 'تم إضافة نقطة ولاء جديدة في صالون :salon_name',
            ],
            'won_reward' => [
                'title' => 'مبروك! لقد ربحت هدية',
                'body' => 'تهانينا! لقد جمعت 5 نقاط ولاء في صالون :salon_name وحصلت على هدية مميزة',
            ],
        ],
    ],


];
