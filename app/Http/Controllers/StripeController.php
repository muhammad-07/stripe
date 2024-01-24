<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\SetupIntent;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.test_keys_india.secret_key'));
    }
    public function setupIntent()
    {
        $intent = \Stripe\SetupIntent::create();
        return response()->json($intent);
    }
    public function saveCard(Request $request)
    {
        // Set your secret key
        // Stripe::setApiKey('your-secret-key');

        $setupIntentId = $request->input('setupIntentId');

        // Save the SetupIntent ID in your database
        // Associate it with the user or another identifier
        // You may also associate the SetupIntent with a customer in Stripe

        return response()->json(['success' => $setupIntentId]);
    }
    public function processPayment(Request $request)
    {
        // Set your secret key
        Stripe::setApiKey('your-secret-key');

        $setupIntentId = $request->input('setupIntentId');

        // Create a PaymentIntent using the SetupIntent
        $paymentIntent = PaymentIntent::create([
            'payment_method' => $setupIntentId,
            'amount' => 1000, // Example amount in cents
            'currency' => 'usd',
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);

        // Handle the PaymentIntent status
        if ($paymentIntent->status === 'requires_action' || $paymentIntent->status === 'requires_source_action') {
            // 3D Secure authentication is required
            return response()->json(['requires_action' => true, 'payment_intent_client_secret' => $paymentIntent->client_secret]);
        } elseif ($paymentIntent->status === 'succeeded') {
            // Payment succeeded
            return response()->json(['success' => true]);
        } else {
            // Payment failed
            return response()->json(['error' => 'Payment failed']);
        }
    }

    function createCustomer(Request $request)
    {
// return $request->payment_method;
        try {
            $customer = \Stripe\Customer::create([
                'payment_method' => $request->payment_method,
            ]);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
        return response()->json($customer);
    }
}
