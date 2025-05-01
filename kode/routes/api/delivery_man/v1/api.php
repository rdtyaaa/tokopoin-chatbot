<?php

use App\Http\Controllers\Api\Delivery\Auth\LoginController;
use App\Http\Controllers\Api\Delivery\Auth\PasswordResetController;
use App\Http\Controllers\Api\Delivery\Auth\RegisterController;
use App\Http\Controllers\Api\Delivery\CustomerChatController;
use App\Http\Controllers\Api\Delivery\DeliveryManChatController;
use App\Http\Controllers\Api\Delivery\HomeController as DeliveryHomeController;
use App\Http\Controllers\Api\Delivery\ProfileController;
use App\Http\Controllers\Api\Delivery\SupportTicketController;
use App\Http\Controllers\Api\Delivery\WithdrawController;
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

  

Route::group(['middleware' => ['api.lang','api.currency','sanitizer','maintenance.mode','handle.exception','deliveryman.module.checker']], function() {


    # PUBLIC ROUTE 
    Route::get('/config',[DeliveryHomeController::class , 'config']);

    Route::post('/login',[LoginController::class,'login']);
    Route::post('/register',[RegisterController::class,'register']);

    #PASSWORD RESET ROUTE
    Route::post('/verify/phone',[PasswordResetController::class,'verifyPhone']);
    Route::post('/verify/otp/code',[PasswordResetController::class,'verifyCode']);
    Route::post('/password/reset',[PasswordResetController::class,'passwordReset']);


    # PROTECTED ROUTE
    Route::group(['middleware' => ['auth:sanctum','deliveryman.api.token','deliveryman.kyc']],function (){

        # HOME ROUTES
        Route::controller(DeliveryHomeController::class)->group(function(){
            
            Route::get('/home','home');
            Route::get('/orders','orders');
            Route::get('/requested/order','requestedOrders');
            Route::get('/order/details/{order_number}','orderDetails');
            Route::get('/transactions','transactions');
            Route::get('/earnings','earnings');
            Route::post('update/fcm-token', 'updateFcmToken');
            Route::post('update/active-status', 'activeStatusUpdate');
            Route::post('update/push-notification-stauts', 'pushNotificationStatusUpdate');
            Route::post('kyc/applications', 'kycApplication')->withoutMiddleware(['deliveryman.kyc']);
            Route::get('kyc/log', 'kycLog')->withoutMiddleware(['deliveryman.kyc']);
            Route::get('get/deliverymen', 'getDeliverymen');
            Route::get('get/analytics', 'analytics');
            Route::post('/assign/order', 'assignOrder');
            Route::post('/order/request/handle', 'handleRequestedOrder');
            Route::post('/order/handle', 'handleOrder');
            Route::get('/reward/point', 'rewardPoint');
            Route::get('/redeem/point', 'redeemPoint');
            Route::get('/get/assigned/orders', 'getAssignedOrder');
            Route::get('/get/referral/log', 'getReferralLog');

        });

        #TICKET ROUTES
        Route::controller(SupportTicketController::class)->prefix('ticket')->group(function(){
            Route::get('/list','list');
            Route::get('/{ticket_number}/messages','ticketMessages');
            Route::get('/{ticket_number}/close','close');
            Route::post('/store','store');
            Route::post('/reply','reply');
            Route::post('/file/download','download');
        });

        #WITHDRAW ROUTE
        Route::controller(WithdrawController::class)->prefix('withdraw')->group(function(){
            Route::get('/methods','methods');
            Route::get('/list','list');
            Route::post('/request','request');
            Route::post('/store','store');
        });

        #CUSTOMER CHAT ROUTE
        Route::controller(CustomerChatController::class)->prefix('customer/chat')->group(function(){
            Route::get('/list','list');
            Route::get('/messages/{customer_id}','getChat');
            Route::post('/send/message','sendMessage');
            Route::get('/delete/conversation/{customer_id}','deleteConversation');
        });

        #DELIVERY MAN CHAT ROUTE
        Route::controller(DeliveryManChatController::class)->prefix('deliveryman/chat')->group(function(){
            Route::get('/list','list');
            Route::get('/messages/{deliverman_id}','getChat');
            Route::post('/send/message','sendMessage');
        });

        #PROFILE & PASSWORD ROUTES
        Route::controller(ProfileController::class)->group(function(){
            Route::post('/referral-code/update','referralCodeUpdate');
            Route::post('/profile/update','update');
            Route::post('/password/update','passwordUpdate');
            Route::post('/logout','logout');
        });

    });
  


});




