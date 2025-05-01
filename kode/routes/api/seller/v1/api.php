<?php

use App\Http\Controllers\Api\Seller\Auth\LoginController;
use App\Http\Controllers\Api\Seller\Auth\PasswordResetController;
use App\Http\Controllers\Api\Seller\Auth\RegisterController;
use App\Http\Controllers\Api\Seller\CustomerChatController;
use App\Http\Controllers\Api\Seller\HomeController;
use App\Http\Controllers\Api\Seller\OrderController;
use App\Http\Controllers\Api\Seller\ProductController;
use App\Http\Controllers\Api\Seller\ProfileController;
use App\Http\Controllers\Api\Seller\SellerChatController;
use App\Http\Controllers\Api\Seller\SubscriptionController;
use App\Http\Controllers\Api\Seller\SupportTicketController;
use App\Http\Controllers\Api\Seller\WithdrawController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

  

Route::group(['middleware' => ['api.lang','api.currency','sanitizer','sellerMode.status.check','maintenance.mode','handle.exception']], function() {



    # PUBLIC ROUTE 
    Route::get('/config',[HomeController::class , 'config']);
    Route::post('/register',[RegisterController::class,'store']);
    Route::post('/login',[LoginController::class,'login']);

    # PASSWORD RESET ROUTE

    Route::post('/verify/email',[PasswordResetController::class,'verifyEmail']);
    Route::post('/verify/otp/code',[PasswordResetController::class,'verifyCode']);
    Route::post('/password/reset',[PasswordResetController::class,'passwordReset']);


    # PROTECTED ROUTE

    Route::group(['middleware' => ['auth:sanctum','seller.api.token','seller.kyc']],function (){

        # HOME ROUTES
        Route::controller(HomeController::class)->group(function(){

            Route::get('/dashboard','dashboard');
            Route::get('/shop','shop');
            Route::get('/deposit/logs','depositLog');
            Route::post('/make/deposit','makeDeposit');
            Route::post('/shop/update','shopUpdate');
            Route::get('/transactions','transactions');
            Route::get('/campaigns','campaigns');
            Route::get('/auth/config','authConfig');
            Route::post('update/fcm-token', 'updateFcmToken');
            Route::post('kyc/applications', 'kycApplication')->withoutMiddleware(['seller.kyc']);
            Route::get('kyc/log', 'kycLog')->withoutMiddleware(['seller.kyc']);
        });


        # TICKET ROUTES
        Route::controller(SupportTicketController::class)->prefix('ticket')->group(function(){
            Route::get('/list','list');
            Route::get('/{ticket_number}/messages','ticketMessages');
            Route::post('/store','store');
            Route::post('/reply','reply');
            Route::post('/file/download','download');
        });


        # SUBSCRIPTION PLAN ROUTE
        Route::controller(SubscriptionController::class)->prefix('subscription')->group(function(){

            Route::get('/plan','plan');
            Route::get('/list','list');
            Route::post('/subscribe','subscribe');
            Route::post('/update','update');
            Route::get('/renew/{uid}','renew');

        });

        #WITHDRAW ROUTE
        Route::controller(WithdrawController::class)->prefix('withdraw')->group(function(){
            Route::get('/methods','methods');
            Route::get('/list','list');
            Route::post('/request','request');
            Route::post('/store','store');

        });


        # ORDER LIST
        Route::controller(OrderController::class)->prefix('order')->group(function(){
            Route::get('/{type}/list','list');
            Route::get('/details/{order_number}','details');
            Route::post('/status/update','statusUpdate');
        });


        # PRODUCT LIST
        Route::controller(ProductController::class)->prefix('product')->group(function(){

            Route::get('/{type}/list','list');
            Route::get('/details/{uid}','details');
            Route::get('/delete/{uid}','delete');
            Route::get('/restore/{uid}','restore');
            Route::get('/permanent/delete/{uid}','permanentDelete');
            Route::post('/store','store');
            Route::post('/update','update');
            Route::get('/gallery/delete/{id}','galleryDelete');
            Route::post('/stock/update/','stockUpdate');

            # DIGITAL PRODUCT 
            Route::prefix('digital')->group( function () {

                Route::post('/store','digitalStore');
                Route::post('/update','digitalUpdate');
                Route::post('/attribute/store','attributeStore');
                Route::post('/attribute/update','attributeUpdate');
                Route::get('/attribute/delete/{uid}','attributeDelete');
                Route::post('/attribute/value/store','attributeValueStore');
                Route::post('/attribute/value/update','attributeValueUpdate');
                Route::get('/attribute/value/delete/{uid}','attributeValueDelete');
            }); 
        });

        # PROFILE & PASSWORD ROUTES
        Route::controller(ProfileController::class)->group(function(){
            Route::post('/profile/update','update');
            Route::post('/password/update','passwordUpdate');
            Route::post('/logout','logout');
        });

        #SELLER CHAT ROUTE
        Route::controller(CustomerChatController::class)->prefix('customer/chat')->group(function(){
            Route::get('/list','list');
            Route::get('/messages/{customer_id}','getChat');
            Route::post('/send/message','sendMessage');
        });

    });
  


});




