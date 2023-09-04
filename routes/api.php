<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RedisCartController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeProductController;
use App\Http\Controllers\StripeSubscriptionController;
use App\Http\Controllers\StripeUserController;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Forgot Password
Route::post('/password/email', [AuthController::class, 'passwordEmail']);
Route::post('/password/reset/', [AuthController::class, 'passwordReset'])->name('password.reset');

Route::middleware(['auth:api'])->group(function () {

    //Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    //Get Details of the currently authenticated user
    Route::get('/user', function(){
        $user = auth()->user();
        return $user;
    });

    //Courses
    Route::post('/courses/{id}', [CourseController::class, 'update']);
    Route::apiResource('/courses', CourseController::class);

    //Courses
    Route::post('/lessons/{id}', [LessonController::class, 'update']);
    Route::apiResource('/lessons', LessonController::class);

    //Stripe Checkout
    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');

    //Stripe Customer
    Route::post('/customer', [StripeUserController::class, 'createCustomer']);

    //Stripe Products
    Route::post('/products', [StripeProductController::class, 'createProduct']);

    //Orders
    Route::apiResource('/orders', OrderController::class);

    //Cart
    Route::get('/cart', [RedisCartController::class, 'index']);
    Route::get('/cart/total', [RedisCartController::class, 'cartTotal']);
    Route::post('/cart', [RedisCartController::class, 'store']);
    Route::delete('/cart', [RedisCartController::class, 'destroy'])->name('cart.delete');


});

Route::get('/success', [StripeController::class, 'success'])->name('success');
Route::get('/cancel', [StripeController::class, 'cancel'])->name('cancel');
Route::post('/webhook', [StripeController::class, 'webhook'])->name('checkout.webhook');