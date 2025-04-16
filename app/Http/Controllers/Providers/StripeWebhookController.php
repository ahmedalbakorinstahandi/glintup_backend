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

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }


        // التعامل مع الحدث
        switch ($event->type) {
            case 'payment_intent.succeeded':
                // log the event
                Log::info('PaymentIntent was successful!');
                // log metadata
                Log::info('Metadata: ' . json_encode($event->data->object->metadata));

                $session = $event->data->object;

                // $checkoutSessionId = $session->id;
                // $paymentIntentId = $session->payment_intent;

                // metadata in object i have my transaction id
                $checkoutSessionId = $session->id;
                $paymentIntentId = $session->payment_intent;

                // Extract metadata from the Stripe session object
                $transactionId = $session->metadata->transaction_id ?? null;
                $type = $session->metadata->type ?? null;



                $walletTransaction = WalletTransaction::find($transactionId);

                if ($walletTransaction && $walletTransaction->status == 'pending') {

                    $metadata = $walletTransaction->metadata ?? [];
                    $metadata['stripe_payment_id'] = $paymentIntentId;

                    $walletTransaction->update([
                        'status' => 'completed',
                        'metadata' => $metadata,
                    ]);

                    if ($type == 'ad') {

                        $adId = $session->metadata->ad_id ?? null;

                        $ad = PromotionAd::find($adId);

                        if ($ad) {
                            $ad->update([
                                'status' => 'in_review',
                            ]);
                        }
                    }


                    // TODO send notification to admin 
                }

                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                Log::warning('Payment failed: ' . $paymentIntent->id);

                $walletTransaction = WalletTransaction::where('metadata->stripe_payment_id', $paymentIntent->id)->first();

                if ($walletTransaction) {
                    $walletTransaction->update([
                        'status' => 'failed',
                    ]);
                }

                break;

            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }
}
