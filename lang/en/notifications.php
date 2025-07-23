<?php

return [
    'admin' => [
        'menu_request' => [
            'new_menu_request' => 'There is a new menu request',
            'new_menu_request_body' => 'The provider :salon_name has requested a new menu, request number :menu_request_id, please check it',
        ],
        'complaint' => [
            'new_complaint' => 'There is a new complaint',
            'new_complaint_body' => 'The user :full_user_name has submitted a complaint, content: :content',
        ],
        'ad' => [
            'new_ad' => 'There is a new advertisement',
            'new_ad_body' => 'The provider :salon_name has added a new advertisement, advertisement number :promotion_ad_id, please check it',
        ],
        'review' => [
            'new_review' => 'There is a new review',
            'new_review_body' => 'The user :full_user_name has added a new review, content: :review_content',
        ],
        'employee_permission' => [
            'update_employee_permission_title' => 'Employee permission updated',
            'update_employee_permission_body' => 'Your permissions have been updated by :admin_user_name',
        ],
        'user' => [
            'new_user' => 'There is a new user',
            'new_user_body' => 'The user :full_user_name has been created, please check it',
        ],

    ],

    'salon' => [
        'ad' => [
            'approve_ad' => 'Ad approved',
            'approve_ad_body' => 'The ad :ad_title in the salon :salon_name has been approved',

            'reject_ad' => 'Ad rejected',
            'reject_ad_body' => 'The ad :ad_title in the salon :salon_name has been rejected',
        ],
        'review' => [
            'new_review' => 'There is a new review',
            'new_review_body' => 'The user :full_user_name has added a new review, content: :review_content',
        ],
        'menu_request' => [
            'accept_title' => 'Menu request accepted',
            'accept_body' => 'The menu request :menu_request_id in the salon :salon_name has been accepted',

            'reject_title' => 'Menu request rejected',
            'reject_body' => 'The menu request :menu_request_id in the salon :salon_name has been rejected',
        ],
        'booking' => [
            'new_booking' => 'There is a new booking',
            'new_booking_body' => 'The user :full_user_name has created a new booking in the salon :salon_name',
        ],
    ],

    'user' => [
        'gift_card' => [
            'new' => [
                'title' => 'You have a new gift card',
                'body' => 'You have received a gift card from :sender',
            ],
            'receive' => [
                'title' => 'Your friend opened your gift',
                'body' => 'Your friend, :sender, opened your gift. Thanks for spreading happiness and inviting them to GlintUp!',
            ],
        ],
        'booking' => [
            'completed' => [
                'title' => 'Booking #:booking_id completed successfully',
                'body' => 'We are pleased to inform you that your booking at :salon_name has been completed successfully. We hope you had a great experience. We look forward to seeing you again soon!',
            ],
            'cancelled' => [
                'title' => 'Booking #:booking_id cancelled',
                'body' => 'We are sorry to inform you that your booking at :salon_name has been cancelled. If you have any questions, please contact customer service. You can always reschedule for another time that suits you.',
            ],
            'rejected' => [
                'title' => 'Booking #:booking_id rejected',
                'body' => 'We are sorry to inform you that your booking at :salon_name has been rejected. This may be due to a busy schedule or unexpected circumstances. We hope you try again later or choose another salon.',
            ],
            'new_booking' => [
                'title' => 'New booking created for you',
                'body' => 'A new booking has been created for you at :salon_name',
            ],
        ],

        'booking_service' => [
            'completed' => [
                'title' => 'Service #:service_name completed in booking #:booking_id',
                'body' => 'Service #:service_name completed in booking #:booking_id at :salon_name',
            ],
        ],

        'loyalty_point' => [
            'added_one_point' => [
                'title' => 'You have a new loyalty point',
                'body' => 'You have received a loyalty point from :salon_name',
            ],
            'won_reward' => [
                'title' => 'You have won a reward',
                'body' => 'You have won a reward from :salon_name',
            ],
        ],




    ],
];
