<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
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
                $paymentIntent = $event->data->object;
                // ✅ نفذ منطق نجاح الدفع: مثال - حدث حالة الحجز أو اشحن المحفظة
                Log::info('Payment succeeded: ' . $paymentIntent->id);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                Log::warning('Payment failed: ' . $paymentIntent->id);
                break;

            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }
}