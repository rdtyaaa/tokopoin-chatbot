<?php

use App\Http\Controllers\Api\DeliverymanChatController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\SellerChatController;
use App\Http\Controllers\Api\WithdrawController;
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


Route::group(['middleware' => ['api.lang','api.currency','sanitizer','maintenance.mode']], function() {

    #AUTH ROUTE
    Route::post('register', [\App\Http\Controllers\Api\Auth\RegisterController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\Api\Auth\LoginController::class, 'login']);
    Route::post('verify-otp', [\App\Http\Controllers\Api\Auth\LoginController::class, 'verifyOTP']);

    #PASSWORD RESET ROUTE
    Route::post('forgot-password', [\App\Http\Controllers\Api\Auth\PasswordResetController::class, 'store']);
    Route::post('reset/password/verify', [\App\Http\Controllers\Api\Auth\PasswordResetController::class, 'verifyOTP']);
    Route::post('reset-password', [\App\Http\Controllers\Api\Auth\PasswordResetController::class, 'resetPassword']);


    Route::group(['middleware' => ['auth:sanctum']], function () {
        
        Route::post('logout', [\App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
        Route::post('add/wishlist', [\App\Http\Controllers\Api\UserController::class, 'wishList']);
        Route::post('delete/wishlist', [\App\Http\Controllers\Api\UserController::class, 'deleteWishlist']);
        Route::post('add/cart', [\App\Http\Controllers\Api\UserController::class, 'addtocart']);
        Route::post('update/fcm-token', [\App\Http\Controllers\Api\UserController::class, 'updateFcmToken']);
        Route::get('cart', [\App\Http\Controllers\Api\UserController::class, 'cart']);
        Route::get('transactions', [\App\Http\Controllers\Api\UserController::class, 'transactions']);
        Route::get('wishlist', [\App\Http\Controllers\Api\UserController::class, 'wishlistItem']);
        Route::post('update/cart', [\App\Http\Controllers\Api\UserController::class, 'updateCart']);
        Route::post('delete/cart', [\App\Http\Controllers\Api\UserController::class, 'deleteCart']);
        Route::get('dashboard', [\App\Http\Controllers\Api\UserController::class, 'dashboard']);
        Route::post('profile/update', [\App\Http\Controllers\Api\UserController::class, 'updateProdile']);
        Route::post('password/update', [\App\Http\Controllers\Api\UserController::class, 'updatePassword']);
        Route::post('review', [\App\Http\Controllers\Api\ProductController::class, 'review']);

        Route::post('address/store', [\App\Http\Controllers\Api\UserController::class,'addressStore']);
        Route::post('address/update',[\App\Http\Controllers\Api\UserController::class,'addressUpdate']);
        Route::get('address/delete/{key}',[\App\Http\Controllers\Api\UserController::class,'addressDelete']);

        Route::post('checkout/complate', [\App\Http\Controllers\Api\UserController::class, 'checkoutSuccess']);

        /** new api */
        Route::get('/order/details/{order_id}', [\App\Http\Controllers\Api\UserController::class, 'orderDetails']);
        Route::get('/shop/flow/{id}', [\App\Http\Controllers\Api\HomeController::class, 'shopFollow']);
        Route::get('/pay/now/{orderUid}/{gateway_code?}', [\App\Http\Controllers\Api\UserController::class, 'payNow']);
        /** support ticket  */
        Route::get('support/tickets', [\App\Http\Controllers\Api\UserController::class,'supportTicket']);
        Route::get('support/ticket/{ticketNumber}', [\App\Http\Controllers\Api\UserController::class, 'ticketDetails']);
        Route::get('support/ticket/file/download/{id}', [\App\Http\Controllers\Api\UserController::class, 'supportTicketDownlod']);
        Route::get('closed/ticket/{ticketNumber}', [\App\Http\Controllers\Api\UserController::class, 'closedTicket']);
        Route::post('support/ticket/store', [\App\Http\Controllers\Api\UserController::class, 'ticketStore']);
        Route::post('ticket/reply/{ticketNumber}', [\App\Http\Controllers\Api\UserController::class, 'ticketReply']);

        Route::post('delivery-man/rating', [\App\Http\Controllers\Api\UserController::class, 'deliveryManRating']);

        #DELIVERYMAN CHAT ROUTE
        Route::controller(DeliverymanChatController::class)->prefix('delivery-man/chat')->group(function(){
            Route::get('/list','list');
            Route::get('/messages/{deliveryman_id}','getChat');
            Route::post('/send/message','sendMessage');
        });

        #SELLER CHAT ROUTE
        Route::controller(SellerChatController::class)->prefix('seller/chat')->group(function(){

            Route::get('/list','list');
            Route::get('/messages/{deliveryman_id}','getChat');
            Route::post('/send/message','sendMessage');
        });

        #WALLET DEPOSIT ROUTE
        Route::controller(DepositController::class)->group(function(){
            Route::get('/deposit/logs','depositLog');
            Route::post('/make/deposit','makeDeposit');
        });

        #WITHDRAW ROUTE
        Route::controller(WithdrawController::class)->prefix('withdraw')->group(function(){
            Route::get('/methods','methods');
            Route::get('/list','list');
            Route::post('/request','request');
            Route::post('/store','store');
        });

        Route::get('reward/log', [\App\Http\Controllers\Api\UserController::class, 'rewardLog']);


        Route::post('reedem/point', [\App\Http\Controllers\Api\UserController::class, 'reedemPoint']);

    });
    
    Route::post('checkout', [\App\Http\Controllers\Api\UserController::class, 'orderCheckout'])->middleware(['guest.checkout']);
    Route::post('digital/checkout', [\App\Http\Controllers\Api\UserController::class, 'DigitalOrderCheckout'])->middleware(['guest.checkout']);
    
    Route::post('track/order', [\App\Http\Controllers\Api\UserController::class, 'trackOrder']);
    Route::get('home', [\App\Http\Controllers\Api\HomeController::class, 'index']);


    Route::get('config', [\App\Http\Controllers\Api\HomeController::class, 'config']);
    Route::get('category/products/{uid}', [\App\Http\Controllers\Api\HomeController::class, 'getCategoryByProduct']);
    Route::get('brand/products/{uid}', [\App\Http\Controllers\Api\HomeController::class, 'brandProduct']);
    Route::get('campaigns/{uid}', [\App\Http\Controllers\Api\HomeController::class, 'campaignDetails']);
    Route::get('product/{uid}/{camp_uid?}', [\App\Http\Controllers\Api\ProductController::class, 'view']);
    Route::get('digital-product/{uid}', [\App\Http\Controllers\Api\ProductController::class, 'digitalProductDetails']);
    Route::get('product-search', [\App\Http\Controllers\Api\ProductController::class, 'search']);
    Route::get('translate/{key}', [\App\Http\Controllers\Api\HomeController::class, 'translate']);

    Route::get('payment/log/{trx_code}', [\App\Http\Controllers\Api\HomeController::class, 'getPaymentLog']);

    Route::get('products', [\App\Http\Controllers\Api\ProductController::class, 'products']);
    
    /** new api */
    Route::get('shop', [\App\Http\Controllers\Api\HomeController::class, 'shop']);
    Route::get('shop/visit/{id}', [\App\Http\Controllers\Api\HomeController::class, 'shopVisit']);

});




