<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
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

class StripeWebhookController extends Controller
{
    private function handlePaymentSuccess($session)
    {
        $type = $session->metadata->type ?? null;

        if ($type === 'booking') {
            return $this->handleBookingPayment($session);
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
                }
                break;

            case 'menu_request':
                $request = SalonSalonMenuRequest::create([
                    'salon_id' => $session->metadata->data_salon_id,
                    'notes' => $session->metadata->data_notes,
                    'cost' => $session->metadata->data_cost,
                    'status' => 'pending',
                ]);
                $walletTransaction->update([
                    'transactionable_id' => $request->id,
                    'transactionable_type' => SalonSalonMenuRequest::class,
                ]);
                break;
        }
    }

    private function handleBookingPayment($session)
    {
        try {
            // Get booking data from metadata
            $bookingData = json_decode($session->metadata->booking_data ?? '', true);
            if (!$bookingData) {
                Log::error('Booking data not found in metadata', ['session' => $session]);
                return;
            }

            // Get user
            $user = User::find($bookingData['user_id']);
            if (!$user) {
                Log::error('User not found', ['user_id' => $bookingData['user_id']]);
                return;
            }

            // Get amount
            $amount = $session->metadata->amount ?? 0;

            // Create booking using BookingService
            $bookingService = app(BookingService::class);
            $booking = $bookingService->createBooking(
                $user,
                $amount,
                $bookingData,
                'stripe',
                $bookingData['use_free_services'],
                json_decode($bookingData['booking_details'], true)
            );

            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'payment_intent' => $session->payment_intent
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session' => $session
            ]);
        }
    }

    private function handlePaymentFailure($session)
    {
        $type = $session->metadata->type ?? null;
        $paymentIntentId = $session->payment_intent;

        // For booking payments, we don't need to do anything since the booking hasn't been created yet
        if ($type === 'booking') {
            Log::info('Booking payment failed', [
                'payment_intent' => $paymentIntentId,
                'metadata' => $session->metadata
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
