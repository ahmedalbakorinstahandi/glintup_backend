<?php

return [
    'welcome' => 'Welcome!',
    'goodbye' => 'Goodbye!',
    'error_occurred' => 'An error occurred.',
    'item_not_found' => 'Item not found.',
    'item_deleted_successfully' => 'Item deleted successfully.',
    'failed_delete_item' => 'Failed to delete item.',
    'item_updated_successfully' => 'Item updated successfully.',
    'item_created_successfully' => 'Item created successfully.',
    'permission_error' => 'You do not have permission.',
    'request_filter_status_error' => 'Request filter status error.',
    'service_request_already_hired' => 'Service provider already hired for this request.',
    'status_updated_successfully' => 'Status updated successfully.',
    'invalid_cancel_request' => 'Invalid cancel request.',
    'invalid_request_status' => 'Invalid request status.',
    'invalid_request' => 'Invalid request.',
    'invalid_credentials' => 'Invalid credentials.',
    'user_not_found' => 'User not found.',
    'user_already_exists' => 'User already exists.',

    'account_created_verify_phone' => 'Account created successfully. Please verify your phone number.',
    'phone_not_found' => 'Phone number not found.',
    'account_verified_successfully' => 'Account verified successfully.',
    'otp_sent_successfully' => 'Verification code sent successfully to your phone number: :phone.',
    'password_reset_successfully' => 'Password reset successfully.',
    'invalid_or_expired_verification_code' => 'Invalid or expired verification code.',
    'unauthorized' => 'Unauthorized access.',
    'user_logged_out_successfully' => 'User logged out successfully.',

    'activation_code_message' => 'Your activation code in GlintUp is: :verifyCode. Please use this code to activate your account. This code will expire in 10 minutes.',
    'password_reset_code_message' => 'Your password reset code in GlintUp is: :verifyCode. Please use this code to reset your password. This code will expire in 10 minutes.',

    'user_logged_in_successfully' => 'User logged in successfully.',
    'service' => [
        'item_created_successfully' => 'Service created successfully',
        'item_updated_successfully' => 'Service updated successfully',
        'item_deleted_successfully' => 'Service deleted successfully',
        'failed_delete_item'        => 'Failed to delete service',
        'item_not_found'            => 'Service not found',
    ],

    'group' => [
        'item_created_successfully' => 'Group created successfully',
        'item_updated_successfully' => 'Group updated successfully',
        'item_deleted_successfully' => 'Group deleted successfully',
        'failed_delete_item'        => 'Failed to delete group',
        'item_not_found'            => 'Group not found',
    ],

    'group_service' => [
        'item_created_successfully' => 'Group service created successfully',
        'item_updated_successfully' => 'Group service updated successfully',
        'item_deleted_successfully' => 'Group service deleted successfully',
        'failed_delete_item'        => 'Failed to delete group service',
        'item_not_found'            => 'Group service not found',
    ],

    'salon' => [
        'item_created_successfully' => 'Salon created successfully',
        'item_updated_successfully' => 'Salon updated successfully',
        'item_deleted_successfully' => 'Salon deleted successfully',
        'failed_delete_item'        => 'Failed to delete salon',
        'item_not_found'            => 'Salon not found',
    ],


    'booking' => [
        'item_created_successfully' => 'Booking created successfully',
        'item_updated_successfully' => 'Booking updated successfully',
        'item_deleted_successfully' => 'Booking deleted successfully',
        'failed_delete_item'        => 'Failed to delete booking',
        'item_not_found'            => 'Booking not found',
        'service_not_in_salon'     => 'Service not in salon',
        'booking_details' => 'Booking #:code - :salon',
        'cannot_reschedule_cancelled_booking' => 'Cannot reschedule a cancelled booking',
        'cannot_reschedule_completed_booking' => 'Cannot reschedule a completed booking',
        'reschedule_successfully' => 'Booking rescheduled successfully',
        'booking_cancelled' => 'Booking #:code - :salon has been cancelled',
        'cancel_successfully' => 'Booking cancelled successfully',
        'already_cancelled_booking' => 'Booking already cancelled',
        'cannot_cancel_completed_booking' => 'Cannot cancel a completed booking',
        'cannot_update_cancelled_booking' => 'Cannot update a cancelled booking',
        'cannot_update_completed_booking' => 'Cannot update a completed booking',
        'slot_not_available' => 'No available slot for service :service_name on :date at :start_time',
    ],


    'user' => [
        'item_created_successfully' => 'User created successfully',
        'item_updated_successfully' => 'User updated successfully',
        'item_deleted_successfully' => 'User deleted successfully',
        'failed_delete_item'        => 'Failed to delete user',
        'item_not_found'            => 'User not found',
        'not_enough_balance' => 'You do not have enough balance in the wallet',
        'is_banned' => 'User is banned',
        'already_registered' => 'User is already registered',
        'registered_but_not_verified' => 'User is registered but not verified',
    ],

    'promotion_ad' => [
        'item_created_successfully' => 'Promotion ad created successfully',
        'item_updated_successfully' => 'Promotion ad updated successfully',
        'item_deleted_successfully' => 'Promotion ad deleted successfully',
        'failed_delete_item'        => 'Failed to delete promotion ad',
        'item_not_found'            => 'Promotion ad not found',
        'request_post_ad_successfully' => 'Request to post ad successfully',
        'ad_already_sent_to_review' => 'Ad already sent to review',
        'ad_sent_to_review_successfully' => 'Ad sent to review successfully',
    ],

    'working_hour' => [
        'item_created_successfully' => 'Working hour created successfully',
        'item_updated_successfully' => 'Working hour updated successfully',
        'item_deleted_successfully' => 'Working hour deleted successfully',
        'failed_delete_item'        => 'Failed to delete working hour',
        'item_not_found'            => 'Working hour not found',
        'this_day_of_week_already_exists' => 'This day of the week already exists for this salon',
    ],

    'notification' => [
        'item_created_successfully' => 'Notification created successfully',
        'item_updated_successfully' => 'Notification updated successfully',
        'item_deleted_successfully' => 'Notification deleted successfully',
        'failed_delete_item'        => 'Failed to delete notification',
        'item_not_found'            => 'Notification not found',
        'send_notification_successfully' => 'Notification sent successfully',
    ],


    'salon_holiday' => [
        'item_created_successfully' => 'Salon holiday created successfully',
        'item_updated_successfully' => 'Salon holiday updated successfully',
        'item_deleted_successfully' => 'Salon holiday deleted successfully',
        'failed_delete_item'        => 'Failed to delete salon holiday',
        'item_not_found'            => 'Salon holiday not found',
    ],



    'social_media_site' => [
        'item_created_successfully' => 'Social media site created successfully',
        'item_updated_successfully' => 'Social media site updated successfully',
        'item_deleted_successfully' => 'Social media site deleted successfully',
        'failed_delete_item'        => 'Failed to delete social media site',
        'item_not_found'            => 'Social media site not found',
    ],

    'salon_customer' => [
        'item_created_successfully' => 'Salon customer created successfully',
        'item_updated_successfully' => 'Salon customer updated successfully',
        'item_deleted_successfully' => 'Salon customer deleted successfully',
        'failed_delete_item'        => 'Failed to delete salon customer',
        'item_not_found'            => 'Salon customer not found',
    ],



    'salon_social_media_site' => [
        'item_created_successfully' => 'Salon social media link created successfully',
        'item_updated_successfully' => 'Salon social media link updated successfully',
        'item_deleted_successfully' => 'Salon social media link deleted successfully',
        'failed_delete_item'        => 'Failed to delete salon social media link',
        'item_not_found'            => 'Salon social media link not found',
    ],


    'review' => [
        'item_created_successfully' => 'Review created successfully',
        'item_updated_successfully' => 'Review updated successfully',
        'item_deleted_successfully' => 'Review deleted successfully',
        'failed_delete_item'        => 'Failed to delete review',
        'item_not_found'            => 'Review not found',
        'replied_successfully'      => 'Review replied successfully',
        'reported_successfully'     => 'Review reported successfully',
        'create_review_error' => 'You cannot create a review without at least one completed booking at this salon, or you already have a review',
    ],

    'ad_payment_description' => 'Payment for ad #:ad_id',
    'wallet_deposit' => 'Wallet deposit amount: :amount',


    'coupon' => [
        'item_created_successfully' => 'Coupon created successfully',
        'item_updated_successfully' => 'Coupon updated successfully',
        'item_deleted_successfully' => 'Coupon deleted successfully',
        'failed_delete_item'        => 'Failed to delete coupon',
        'item_not_found'            => 'Coupon not found',
        'code_already_exists' => 'Code :code already exists',
        'item_checked_successfully' => 'Coupon checked successfully',
        'is_invalid' => 'Coupon is invalid',
    ],

    'wallet_transaction' => [
        'item_created_successfully' => 'Wallet transaction created successfully',
        'item_updated_successfully' => 'Wallet transaction updated successfully',
        'item_deleted_successfully' => 'Wallet transaction deleted successfully',
        'failed_delete_item'        => 'Failed to delete wallet transaction',
        'item_not_found'            => 'Wallet transaction not found',
    ],

    'gift_card' => [
        'item_created_successfully' => 'Gift card created successfully',
        'item_updated_successfully' => 'Gift card updated successfully',
        'item_deleted_successfully' => 'Gift card deleted successfully',
        'failed_delete_item'        => 'Failed to delete gift card',
        'item_not_found'            => 'Gift card not found',
        'cannot_send_to_yourself'   => 'You cannot send a gift card to yourself',
        'transaction_details'       => 'Gift card transaction #:code added with an amount of :amount :currency. Details: :details. We wish you a great experience!',
        'not_your_gift_card'        => 'This is not a gift card for you',
        'item_already_used'         => 'The gift card has already been used',
        'item_already_received'     => 'The gift card has already been received',
        'received_transaction_details' => 'Gift card #:code received with a value of :amount :currency.',
        'item_received_successfully' => 'Gift card received successfully',
    ],

    'gift_card_message' => "Hi,\n\nYou have received a gift card from :sender.\n\nDetails:\n:details\n\nMessage from sender:\n:note\n\nPlease download the app from the link below and register to claim your gift card.\n\n:link",

    'gift_card_amount_details' => "Gift Card Value: :amount :currency",

    'gift_card_service_details' => "Gift Card Services:\n:services",


    'setting' => [
        'item_created_successfully' => 'Setting created successfully',
        'item_updated_successfully' => 'Setting updated successfully',
        'item_deleted_successfully' => 'Setting deleted successfully',
        'failed_delete_item'        => 'Failed to delete setting',
        'item_not_found'            => 'Setting not found',
    ],


    'salon_staff' => [
        'item_created_successfully'       => 'Salon staff member added successfully',
        'item_updated_successfully'       => 'Salon staff member updated successfully',
        'item_deleted_successfully'       => 'Salon staff member deleted successfully',
        'failed_delete_item'              => 'Failed to delete salon staff member',
        'item_not_found'                  => 'Salon staff member not found',
        'permissions_updated_successfully' => 'Salon staff member permissions updated successfully',
    ],


    'salon_payment' => [
        'item_created_successfully' => 'Salon payment created successfully',
        'item_updated_successfully' => 'Salon payment updated successfully',
        'item_deleted_successfully' => 'Salon payment deleted successfully',
        'failed_delete_item'        => 'Failed to delete salon payment',
        'item_not_found'            => 'Salon payment not found',
    ],


    'loyalty_point' => [
        'item_created_successfully' => 'Loyalty points created successfully',
        'item_updated_successfully' => 'Loyalty points updated successfully',
        'item_deleted_successfully' => 'Loyalty points deleted successfully',
        'failed_delete_item'        => 'Failed to delete loyalty points',
        'item_not_found'            => 'Loyalty points not found',
        'already_received'         => 'Loyalty points already received',
        'salon_deos_not_have_loyalty_service' => 'The salon has not specified a loyalty service',
        'item_received_successfully' => 'Loyalty points received successfully',
        'not_enough_points' => 'The coupon must contain 5 stamps',
    ],

    'phone_already_exists' => 'Phone number already exists',
    'salon_registered_successfully' => 'Salon registered successfully',
    'phone_already_taken' => 'Phone number already taken',


    'complaint' => [
        'item_created_successfully' => 'Complaint submitted successfully',
        'item_updated_successfully' => 'Complaint status updated',
        'item_deleted_successfully' => 'Complaint deleted successfully',
        'failed_delete_item'        => 'Failed to delete complaint',
        'item_not_found'            => 'Complaint not found',
    ],

    'salon_menu_request' => [
        'created' => 'Menu request created successfully.',
        'updated' => 'Menu request updated successfully.',
        'deleted' => 'Menu request deleted successfully.',
        'not_found' => 'Menu request not found.',
    ],

    'menu_request_payment_description' => 'Payment for menu request',


    'admin_users' => [
        'item_created_successfully' => 'Admin user created successfully.',
        'item_updated_successfully' => 'Admin user updated successfully.',
        'item_deleted_successfully' => 'Admin user deleted successfully.',
        'failed_delete_item' => 'Failed to delete admin user.',
        'permissions_updated_successfully' => 'Admin user permissions updated successfully.',
        'item_not_found' => 'Admin user not found.',
    ],


    'contact' => [
        'item_created_successfully' => 'Contact created successfully',
        'item_updated_successfully' => 'Contact updated successfully',
        'item_deleted_successfully' => 'Contact deleted successfully',
        'failed_delete_item'        => 'Failed to delete contact',
        'item_not_found'            => 'Contact not found',
    ],
];
