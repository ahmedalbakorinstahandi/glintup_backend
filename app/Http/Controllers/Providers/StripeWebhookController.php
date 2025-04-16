<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Statistics\PromotionAd;
use App\Models\Users\WalletTransaction;
use Google\Cloud\Iam\V1\GetPolicyOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {

        // log
        Log::info('Stripe Webhook Request: ' . json_encode($request->all()));
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\UnexpectedValueException $e) {
            // log
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // log
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // log the event
        Log::info('Stripe Webhook Event: ' . $event->type);

        // التعامل مع الحدث
        switch ($event->type) {
            case 'payment_intent.succeeded':
            case 'checkout.session.completed':
                // log the event
                Log::info('PaymentIntent was successful!');
                // log metadata
                Log::info('Metadata: ' . json_encode($event->data->object->metadata));

                $session = $event->data->object;

                // $checkoutSessionId = $session->id;
                // $paymentIntentId = $session->payment_intent;

                // metadata in object i have my transaction id
                $paymentIntentId = $session->payment_intent;

                // Extract metadata from the Stripe session object
                $transactionId = $session->metadata->transaction_id ?? null;
                $type = $session->metadata->type ?? null;



                $walletTransaction = WalletTransaction::find($transactionId);

                if ($walletTransaction && $walletTransaction->status == 'pending') {

                    $metadata = [
                        'stripe_payment_id' => $paymentIntentId,
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

                    // log type
                    Log::info('Type: ' . $type);

                    if ($type == 'ad') {

                        $adId = $session->metadata->ad_id ?? null;

                        $ad = PromotionAd::find($adId);

                        // log ad
                        Log::info('Ad: ' . json_encode($ad));

                        // log ad id and status
                        Log::info('Ad ID: ' . $adId);
                        Log::info('Ad Status: ' . $ad->status);

                        if ($ad) {
                            $ad->update([
                                'status' => 'in_review',
                            ]);
                        }

                        // TODO send notification to admin 
                    }

                    if ($type == 'deposit') {
                        $user = $walletTransaction->user;

                        $user->update([
                            'balance' => $user->wallet_balance + $walletTransaction->amount,
                        ]);
                    }
                }

                break;

            case 'payment_intent.payment_failed':
            case 'checkout.session.expired':

                $session = $event->data->object;


                // metadata in object i have my transaction id
                $paymentIntentId = $session->payment_intent;

                // Extract metadata from the Stripe session object
                $transactionId = $session->metadata->transaction_id ?? null;
                $type = $session->metadata->type ?? null;



                $walletTransaction = WalletTransaction::find($transactionId);

                if ($walletTransaction && $walletTransaction->status == 'pending') {

                    $metadata = $walletTransaction->metadata ?? [];
                    $metadata['stripe_payment_id'] = $paymentIntentId;

                    $walletTransaction->update([
                        'status' => 'failed',
                        'metadata' => $metadata,
                    ]);
                }

                break;

            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }
}
