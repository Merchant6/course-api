<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    
});