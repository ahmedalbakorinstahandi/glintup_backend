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
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET'); // خدها من Stripe بعد إنشاء الويب هوك

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // التعامل مع الحدث
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $session = $event->data->object;

                $checkoutSessionId = $session->id;
                $paymentIntentId = $session->payment_intent;


                $walletTransaction = WalletTransaction::where('metadata->checkout_session', $checkoutSessionId)->first();

                if ($walletTransaction) {
                    $walletTransaction->update([
                        'status' => 'completed',
                        'metadata' => [
                            'stripe_payment_id' => $paymentIntentId,
                        ],
                    ]);

                    if ($walletTransaction->transactionable_type == PromotionAd::class) {
                        $ad = PromotionAd::find($walletTransaction->transactionable_id);

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
