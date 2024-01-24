<?php

use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('save-card', [StripeController::class, 'saveCard']);
Route::get('setup_intent', [StripeController::class, 'setupIntent']);
Route::post('create_customer', [StripeController::class, 'createCustomer']);

