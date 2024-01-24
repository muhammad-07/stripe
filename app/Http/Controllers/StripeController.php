<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Stripe\Customer;
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
    public function retrieveIntent(Request $request)
    {
        $intent = \Stripe\PaymentIntent::retrieve($request->id);
        return response()->json($intent);
    }
    public function chargeCustomer(Request $request){
        try {
            return $intent = \Stripe\PaymentIntent::create([
              'amount' => 1099,
              'currency' => 'usd',
              'payment_method_types' => ['card'],
              'customer' => $request->customer,
              'payment_method' => $request->payment_method,
              'off_session' => true,
              'confirm' => true,
              'description' => 'test payment'
            ]);
          } catch (Exception $e) {
            return response()->json($e->getMessage());
          }
    }
    public function saveCard(Request $request)
    {
        // Set your secret key
        // Stripe::setApiKey('your-secret-key');

        $setupIntentId = $request->input('setupIntentId');

        // Save the SetupIntent ID in your database
        // Associate it with the user or another identifier
        // You may also associate the SetupIntent with a customer in Stripe

        // Retrieve customer ID from your database
        $customerId = "cus_PQqjG4NhyKh601";//$request->user()->stripe_customer_id;

        if (!$customerId) {
            // Customer doesn't exist, create a new customer
            try {
                $customer = \Stripe\Customer::create([
                  'payment_method' => $request->payment_method,
                ]);
            //     $customerId = $customer->id;
            // $request->user()->update(['stripe_customer_id' => $customerId]);
              } catch (Exception $e) {
                return response()->json($e->getMessage());
              }

            // Save the new customer ID in your database

        } else {
            return "cust ex";
            // Customer already exists, add a new card to the existing customer
            // $customer = Customer::retrieve($customerId);
            // $customer->sources->create(['source' => $request->input('stripeToken')]);
        }


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
                'name' => 'Jenny Rosen',
  'address' => [
    'line1' => '510 Townsend St',
    'postal_code' => '98140',
    'city' => 'San Francisco',
    'state' => 'CA',
    'country' => 'US',
  ],
            ]);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
        return response()->json($customer);
    }
}
