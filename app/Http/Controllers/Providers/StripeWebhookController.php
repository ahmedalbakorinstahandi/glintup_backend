<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Http\Loggers\PromotionAdLogger;
use App\Http\Notifications\AdNotification;
use App\Http\Notifications\MenuRequestNotification;
use App\Models\Booking\SalonMenuRequest;
use App\Models\Statistics\PromotionAd;
use App\Models\Booking\WalletTransaction;
use Google\Cloud\Iam\V1\GetPolicyOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Booking\Booking;
use App\Models\Users\WalletTransaction as UserWalletTransaction;
use App\Models\Salons\SalonMenuRequest as SalonSalonMenuRequest;
use App\Http\Services\Booking\BookingService;
use App\Models\Users\User;
use Illuminate\Support\Facades\Cache;
use App\Http\Services\Rewards\GiftCardService;

class StripeWebhookController extends Controller
{
    private function handlePaymentSuccess($session)
    {
        $type = $session->metadata->type ?? null;

        if ($type === 'booking') {
            return $this->handleBookingPayment($session);
        }

        if ($type === 'gift_card') {
            return $this->handleGiftCardPayment($session);
        }

        $transactionId = $session->metadata->transaction_id ?? null;
        if (!$transactionId) {
            return;
        }

        $walletTransaction = UserWalletTransaction::find($transactionId);
        if (!$walletTransaction || $walletTransaction->status !== 'pending') {
            return;
        }

        $metadata = [
            'stripe_payment_id' => $session->payment_intent,
            'phone' => $session->metadata->phone ?? null,
            'user_id' => $session->metadata->user_id ?? null,
            'ad_id' => $session->metadata->ad_id ?? null,
            'salon_id' => $session->metadata->salon_id ?? null,
            'checkout_session' => $session->id,
        ];

        $walletTransaction->update([
            'status' => 'completed',
            'metadata' => $metadata,
        ]);

        switch ($type) {
            case 'deposit':
                $user = $walletTransaction->user;
                $user->update([
                    'balance' => $user->balance + $walletTransaction->amount,
                ]);
                break;

            case 'ad':
                $adId = $session->metadata->ad_id ?? null;
                $ad = PromotionAd::find($adId);
                if ($ad) {
                    $ad->update(['status' => 'in_review']);
                    AdNotification::newAd($ad);

                    // PromotionAdLogger::logCreation($ad);
                }


                break;

            case 'menu_request':
                $request = SalonSalonMenuRequest::create([
                    'salon_id' => $session->metadata->data_salon_id,
                    'notes' => $session->metadata->data_notes,
                    'cost' => $session->metadata->data_cost,
                    'status' => 'pending',
                ]);
                MenuRequestNotification::newMenuRequest($request);
                $walletTransaction->update([
                    'transactionable_id' => $request->id,
                    'transactionable_type' => SalonSalonMenuRequest::class,
                ]);
                break;
        }
    }

    private function handleGiftCardPayment($session)
    {
        // Get payment intent ID - handle both payment_intent and checkout_session
        $paymentIntentId = $session->id ?? $session->payment_intent;

        Log::info('Processing gift card payment', [
            'session_id' => $session->id ?? 'null',
            'session_payment_intent' => $session->payment_intent ?? 'null',
            'final_payment_intent_id' => $paymentIntentId,
            'session_type' => get_class($session)
        ]);

        // Get gift card data from cache using payment intent ID
        $cacheKey = "gift_card_data_{$paymentIntentId}";

        try {
            $giftCardData = Cache::get($cacheKey);

            if (!$giftCardData) {
                Log::error('Gift card data not found in cache', [
                    'payment_intent' => $paymentIntentId,
                    'cache_key' => $cacheKey,
                    'session_type' => get_class($session),
                    'session_id' => $session->id ?? 'null'
                ]);
                return;
            }

            // Get user
            $user = User::find($giftCardData['user_id']);
            if (!$user) {
                Log::error('User not found', ['user_id' => $giftCardData['user_id']]);
                return;
            }

            // Get amount from metadata
            $amount = $session->metadata->amount ?? 0;

            // Create gift card using GiftCardService
            $giftCardService = app(GiftCardService::class);
            $giftCard = $giftCardService->createGiftCard(
                $user,
                $amount,
                $giftCardData,
                $giftCardData['recipient_id'] ? User::find($giftCardData['recipient_id']) : null,
                'stripe'
            );

            // Remove gift card data from cache after successful creation
            Cache::forget($cacheKey);

            Log::info('Gift card created successfully', [
                'gift_card_id' => $giftCard->id,
                'payment_intent' => $paymentIntentId
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating gift card', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_intent' => $paymentIntentId,
                'cache_key' => $cacheKey
            ]);
        }
    }

    private function handleBookingPayment($session)
    {
        // Get payment intent ID - handle both payment_intent and checkout_session
        $paymentIntentId = $session->id ?? $session->payment_intent;

        Log::info('Processing booking payment', [
            'session_id' => $session->id ?? 'null',
            'session_payment_intent' => $session->payment_intent ?? 'null',
            'final_payment_intent_id' => $paymentIntentId,
            'session_type' => get_class($session)
        ]);

        // Get booking data from cache using payment intent ID
        $cacheKey = "booking_data_{$paymentIntentId}";

        try {
            $bookingData = Cache::get($cacheKey);

            if (!$bookingData) {
                Log::error('Booking data not found in cache', [
                    'payment_intent' => $paymentIntentId,
                    'cache_key' => $cacheKey,
                    'session_type' => get_class($session),
                    'session_id' => $session->id ?? 'null'
                ]);
                return;
            }

            // Get user
            $user = User::find($bookingData['user_id']);
            if (!$user) {
                Log::error('User not found', ['user_id' => $bookingData['user_id']]);
                return;
            }

            // Get amount from metadata
            $amount = $session->metadata->amount ?? 0;

            // Create booking using BookingService
            $bookingService = app(BookingService::class);
            $booking = $bookingService->createBooking(
                $user,
                $amount,
                $bookingData,
                'stripe',
                $bookingData['use_free_services'],
                $bookingData['booking_details']
            );

            // Remove booking data from cache after successful creation
            Cache::forget($cacheKey);

            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'payment_intent' => $paymentIntentId
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_intent' => $paymentIntentId,
                'cache_key' => $cacheKey
            ]);
        }
    }

    private function handlePaymentFailure($session)
    {
        $type = $session->metadata->type ?? null;
        $paymentIntentId = $session->id ?? $session->payment_intent;

        // For booking payments, clean up cache data
        if ($type === 'booking') {
            $cacheKey = "booking_data_{$paymentIntentId}";
            Cache::forget($cacheKey);

            Log::info('Booking payment failed - cache cleaned', [
                'payment_intent' => $paymentIntentId,
                'cache_key' => $cacheKey
            ]);
            return;
        }

        // For gift card payments, clean up cache data
        if ($type === 'gift_card') {
            $cacheKey = "gift_card_data_{$paymentIntentId}";
            Cache::forget($cacheKey);

            Log::info('Gift card payment failed - cache cleaned', [
                'payment_intent' => $paymentIntentId,
                'cache_key' => $cacheKey
            ]);
            return;
        }

        $transactionId = $session->metadata->transaction_id ?? null;
        $walletTransaction = UserWalletTransaction::find($transactionId);

        if ($walletTransaction && $walletTransaction->status === 'pending') {
            $metadata = $walletTransaction->metadata ?? [];
            $metadata['stripe_payment_id'] = $paymentIntentId;

            $walletTransaction->update([
                'status' => 'failed',
                'metadata' => $metadata,
            ]);
        }
    }

    public function handleWebhook(Request $request)
    {
        Log::info('Stripe Webhook Request: ' . json_encode($request->all()));
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe Webhook Event: ' . $event->type);

        switch ($event->type) {
            case 'payment_intent.succeeded':
            case 'checkout.session.completed':
                Log::info('PaymentIntent was successful!');
                Log::info('Metadata: ' . json_encode($event->data->object->metadata));
                $this->handlePaymentSuccess($event->data->object);
                break;

            case 'payment_intent.payment_failed':
            case 'checkout.session.expired':
                $this->handlePaymentFailure($event->data->object);
                break;

            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }
}
