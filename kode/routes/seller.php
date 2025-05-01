<?php

use App\Http\Controllers\PaymentMethod\AamarpayPaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\Auth\SellerAuthenticateController;
use App\Http\Controllers\Seller\Auth\RegiterController;
use App\Http\Controllers\Seller\Auth\ForgotPasswordController;
use App\Http\Controllers\Seller\Auth\PasswordResetController;
use App\Http\Controllers\Seller\HomeController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\SubscriptionPlanController;
use App\Http\Controllers\Seller\SupportTicketController;
use App\Http\Controllers\Seller\DigitalProductController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\DigitalProductOrderController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Seller\CustomerChatController;

use App\Http\Controllers\Seller\DepositController;

$globalMiddleware = ['sanitizer',"dos.security" ,'sellerMode.status.check'];
try {
    DB::connection()->getPdo();
    if(DB::connection()->getDatabaseName()){
        array_push($globalMiddleware , 'throttle:refresh');
    }
} catch (\Exception $ex) {
    //throw $th;
}



Route::middleware($globalMiddleware)->group(function(){

    Route::prefix('seller')->name('seller.')->group(function () {
        Route::get('/', [SellerAuthenticateController::class, 'showLogin'])->name('login');
        Route::post('authenticate', [SellerAuthenticateController::class, 'authenticate'])->name('authenticate');
        Route::get('logout', [SellerAuthenticateController::class, 'logout'])->name('logout');
        Route::get('/register', [RegiterController::class, 'register'])->name('register')->middleware('sellerRegAllow');
        Route::post('/register/store', [RegiterController::class, 'store'])->name('store');

        Route::get('/reset/password', [ForgotPasswordController::class, 'create'])->name('reset.password.request');
        Route::post('/reset/password/store', [ForgotPasswordController::class, 'store'])->name('reset.password.store');
        Route::get('/password/verify/code', [ForgotPasswordController::class, 'passwordResetCodeVerify'])->name('password.verify.code');
        Route::post('/email/verification/code', [ForgotPasswordController::class, 'emailVerificationCode'])->name('email.verification.code');
        Route::get('/password/reset/{token}', [PasswordResetController::class, 'resetPassword'])->name('password.reset.token');
        Route::post('/password/reset/store', [PasswordResetController::class, 'store'])->name('password.reset.store');


        Route::middleware(['seller', 'checkSellerStatus','seller.kyc'])->group(function () {
            //Profile Update
            Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
            Route::get('profile', [HomeController::class, 'profile'])->name('profile');
            Route::post('profile/update', [HomeController::class, 'profileUpdate'])->name('profile.update');
            Route::post('password/update', [HomeController::class, 'passwordUpdate'])->name('password.update');

            //Shop Setting
            Route::get('shop/setting', [HomeController::class, 'shopSetting'])->name('shop.setting');
            Route::post('shop/setting/update/{id}', [HomeController::class, 'shopSettingUpdate'])->name('shop.setting.update');
            Route::get('withdraw/method', [HomeController::class, 'withdrawMethod'])->name('withdraw.method');
            Route::post('withdraw/money', [HomeController::class, 'withdrawMoney'])->name('withdraw.money');
            Route::get('withdraw/preview/{trxnumber}', [HomeController::class, 'withdrawPreview'])->name('withdraw.preview');
            Route::post('withdraw/preview/store/{id}', [HomeController::class, 'withdrawPreviewStore'])->name('withdraw.preview.store');
            Route::get('withdraw/log', [HomeController::class, 'withdrawHistory'])->name('withdraw.history');
            Route::get('withdraw/show/{id}', [HomeController::class, 'withdrawShow'])->name('withdraw.show');

            #SELLER CHAT ROUTE
            Route::controller(CustomerChatController::class)->prefix('customer/chat')->group(function(){
                Route::get('/list','list')->name('customer.chat.list');
                Route::get('/messages/{customer_id}','getChat')->name('customer.chat.message');
                Route::post('/send/message','sendMessage')->name('customer.chat.send_message');
            }); 

            //Transaction Log
            Route::get('transaction/log', [HomeController::class, 'transaction'])->name('transaction.history');

            //Product
            Route::get('products/index', [ProductController::class, 'index'])->name('product.index');
            Route::get('products/approved', [ProductController::class, 'approved'])->name('product.approved');
            Route::get('products/refuse', [ProductController::class, 'refuse'])->name('product.refuse');
            Route::get('products/trashed', [ProductController::class, 'trashed'])->name('product.trashed');
            Route::get('add/physical/products', [ProductController::class, 'create'])->name('product.create');
            Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
            Route::get('product/edit/{id}/{slug}', [ProductController::class, 'edit'])->name('product.edit');
            Route::get('product/details/{id}/{slug}', [ProductController::class, 'details'])->name('product.details');
            Route::post('product/update/{id}', [ProductController::class, 'update'])->name('product.update');
            Route::post('product/delete', [ProductController::class, 'delete'])->name('product.delete');
            Route::post('product/restore', [ProductController::class, 'restore'])->name('product.restore');
            //permanent delete
            Route::post('product/permanent-delete', [ProductController::class, 'permanentDelete'])->name('product.permanentDelete');

            Route::post('/product/combination', [ProductController::class, 'combination'])->name('product.combination');
            Route::post('/product/attribute', [ProductController::class, 'attrValue'])->name('product.attribute.value');
            Route::post('/product/attribute/edit', [ProductController::class, 'stock_edit'])->name('product.attribute.edit');

            Route::post('product/status/update', [ProductController::class, 'statusUpdate'])->name('product.status.update');


            Route::get('product/stock/{id}', [ProductController::class, 'stock'])->name('product.stock');
            Route::post('product/stock/update/{id}', [ProductController::class, 'productStockUpdate'])->name('product.stock.update');
            Route::get('product/gallery/image/delete/{id}', [ProductController::class, 'productGalleryImageDelete'])->name('product.gallery.image.delete');

            Route::get('product/single/all/order/{id}', [ProductController::class, 'singleProductAllOrder'])->name('product.single.all.order');
            Route::get('product/single/placed/order/{id}', [ProductController::class, 'singleProductPlacedOrder'])->name('product.single.placed.order');
            Route::get('product/single/delivered/order/{id}', [ProductController::class, 'singleProductDeliveredOrder'])->name('product.single.delivered.order');
            Route::get('product/search/{scope}', [ProductController::class, 'search'])->name('product.search');

            //Digital Product
            Route::get('digital/products', [DigitalProductController::class, 'index'])->name('digital.product.index');
            Route::get('digital/new/products', [DigitalProductController::class, 'new'])->name('digital.product.new');
            Route::get('digital/approved/products', [DigitalProductController::class, 'approved'])->name('digital.product.approved');
            Route::get('digital/product/create', [DigitalProductController::class, 'create'])->name('digital.product.create');
            Route::post('digital/product/store', [DigitalProductController::class, 'store'])->name('digital.product.store');
            Route::post('digital/product/delete', [DigitalProductController::class, 'delete'])->name('digital.product.delete');
            Route::get('digital/product/edit/{id}', [DigitalProductController::class, 'edit'])->name('digital.product.edit');
            Route::post('digital/product/update/{id}', [DigitalProductController::class, 'update'])->name('digital.product.update');
            Route::post('digital/product/delete/', [DigitalProductController::class, 'delete'])->name('digital.product.delete');
            Route::post('digital/product/restore/', [DigitalProductController::class, 'restore'])->name('digital.product.restore');



            Route::post('digital/product/attribute/update', [DigitalProductController::class, 'attributeUpdate'])->name('digital.product.attribute.update');


            Route::post('digital/product/attribute/delete', [DigitalProductController::class, 'attributeDelete'])->name('digital.product.attribute.delete');


            Route::get('digital/product/attribute/{id}', [DigitalProductController::class, 'attribute'])->name('digital.product.attribute');
            Route::post('digital/product/attribute/store', [DigitalProductController::class, 'attributeStore'])->name('digital.product.attribute.store');
            Route::get('digital/product/attribute/edit/{id}', [DigitalProductController::class, 'attributeEdit'])->name('digital.product.attribute.edit');

            
            Route::post('digital/product/attribute/value/store/{id}', [DigitalProductController::class, 'attributeValueStore'])->name('digital.product.attribute.value.store');

            Route::post('digital/product/attribute/value/update', [DigitalProductController::class, 'attributeValueUpdate'])->name('digital.product.attribute.value.update');


            Route::post('digital/product/attribute/value/delete', [DigitalProductController::class, 'attributeValueDelete'])->name('digital.product.attribute.value.delete');

            Route::get('digital/product/attribute/value/download/{id}', [DigitalProductController::class, 'attributeValueDownload'])->name('digital.product.attribute.value.download');


            //Pricing Plan
            Route::get('plans', [SubscriptionPlanController::class, 'index'])->name('plan.index');
            Route::get('plan/subscribe', [SubscriptionPlanController::class, 'plan'])->name('plan.subscribe');
            Route::post('plan/subscription', [SubscriptionPlanController::class, 'subscription'])->name('plan.subscription');
            Route::post('plan/update/request', [SubscriptionPlanController::class, 'planUpdateRequest'])->name('plan.update.request');
            Route::post('plan/renew', [SubscriptionPlanController::class, 'renewPlan'])->name('plan.renew');

            # Ticket route
            Route::get('support/tickets', [SupportTicketController::class, 'index'])->name('ticket.index');
            Route::get('support/create/new/ticket', [SupportTicketController::class, 'create'])->name('ticket.create');
            Route::post('support/ticket/store', [SupportTicketController::class, 'store'])->name('ticket.store');
            Route::get('support/ticket/reply/{id}', [SupportTicketController::class, 'detail'])->name('ticket.detail');
            Route::post('support/ticket/reply/{id}', [SupportTicketController::class, 'ticketReply'])->name('ticket.reply');
            Route::post('support/closed/{id}', [SupportTicketController::class, 'closedTicket'])->name('ticket.closed');
            Route::get('support/ticket/file/download/{id}', [SupportTicketController::class, 'supportTicketDownlode'])->name('ticket.file.download');

            //Physical Product Order
            Route::get('orders', [OrderController::class, 'index'])->name('order.index');
            Route::get('placed/orders', [OrderController::class, 'placed'])->name('order.placed');
            Route::get('return/orders', [OrderController::class, 'return'])->name('order.return');
            Route::get('pending/orders', [OrderController::class, 'pending'])->name('order.pending');
            Route::get('confirmed/orders', [OrderController::class, 'confirmed'])->name('order.confirmed');
            Route::get('processing/orders', [OrderController::class, 'processing'])->name('order.processing');
            Route::get('shipped/orders', [OrderController::class, 'shipped'])->name('order.shipped');
            Route::get('delivered/orders', [OrderController::class, 'delivered'])->name('order.delivered');
            Route::get('cancel/orders', [OrderController::class, 'cancel'])->name('order.cancel');
            Route::get('order/details/{id}', [OrderController::class, 'details'])->name('order.details');
            Route::post('order/status/update/{id}', [OrderController::class, 'orderStatusUpdate'])->name('order.status.update');
            Route::get('order/print/{id}', [OrderController::class, 'printInvoice'])->name('order.print');

            //Digital Product Order
            Route::get('digital/orders', [DigitalProductOrderController::class, 'index'])->name('digital.order.index');
            Route::get('digital/orders/search', [DigitalProductOrderController::class, 'search'])->name('digital.order.search');
            Route::get('digital/orders/date/search', [DigitalProductOrderController::class, 'dateSearch'])->name('digital.order.date.search');
            Route::get('digital/orders/details/{id}', [DigitalProductOrderController::class, 'details'])->name('digital.order.details');

            /** kyc */
            Route::controller(HomeController::class)
            ->withoutMiddleware(['seller.kyc'])
            ->prefix("/kyc")
            ->name('kyc.')
            ->group(function(){
                Route::get('form','kycForm')->name('form');
                Route::post('apply','kycApplication')->name('apply');
            });


            Route::controller(HomeController::class)
                ->withoutMiddleware(['seller.kyc'])
                ->prefix("/kyc")
                ->name('kyc.log.')
                ->group(function(){
                    Route::get('list','kycList')->name('list');
                    Route::get('show/{id}','kycShow')->name('show');
            });




        /**
         * Wallet deposit route
         */
         Route::controller(DepositController::class)
            ->prefix("/deposit")
            ->name('deposit.')
            ->group(function(){
                Route::get('list','list')->name('list');
                Route::get('method','method')->name('method');
                Route::get('show/{id}','show')->name('show');
                Route::post('money','money')->name('money');
                Route::post('manual/request','manualDeposit')->name('manual.request');
                Route::get('custom/view/{code}/{trx_code}','customView')->name('custom.view');
        });


        #DEPOSIT ROUTE 

        Route::get('/deposit/success/{trx_number}', [DepositController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/deposit/failed/{trx_number}', [DepositController::class, 'paymentFailed'])->name('payment.failed');





        });
    });
});

