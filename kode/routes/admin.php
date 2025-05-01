<?php

use App\Http\Controllers\Admin\AddonManagerController;
use App\Http\Controllers\Admin\TaxController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\PricingPlanController;
use App\Http\Controllers\Admin\SellerProductController;
use App\Http\Controllers\Admin\WithdrawMethodController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\WithdrawController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\MailConfigurationController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\InhouseProductOrderController;
use App\Http\Controllers\Admin\SellerProductOrderController;
use App\Http\Controllers\Admin\SellerShopController;
use App\Http\Controllers\Admin\DigitalProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DigitalProductOrderController;
use App\Http\Controllers\Admin\PromoteController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\FrontendController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PageSetUpController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\NewsLatterController;
use App\Http\Controllers\Admin\SeoContentController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\SmsGatewayController;
use App\Http\Controllers\Admin\SmsTemplateController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DeliveryManController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\SystemUpdateController;
use App\Http\Middleware\ManualUpdateMiddleware;
use Illuminate\Support\Facades\DB;



$globalMiddleware = ['sanitizer', 'demo'];

Route::prefix('/admin')->name('admin.')->middleware($globalMiddleware)->group(function () {

    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::post('authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('forgot-password', [NewPasswordController::class, 'create'])->name('password.request');
    Route::post('password/email', [NewPasswordController::class, 'store'])->name('password.email');
    Route::get('password/verify/code', [NewPasswordController::class, 'passwordResetCodeVerify'])->name('password.verify.code');
    Route::post('password/code/verify', [NewPasswordController::class, 'emailVerificationCode'])->name('email.password.verify.code');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset/password', [ResetPasswordController::class, 'store'])->name('password.reset.update');

    Route::middleware('admin')->group(function () {

        #Manage Admin
        Route::controller(AdminController::class)->group(function () {
            Route::get('dashboard', 'index')->name('dashboard');
            Route::get('profile', 'profile')->name('profile');
            Route::post('profile/update', 'profileUpdate')->name('profile.update');
            Route::post('password/update', 'passwordUpdate')->name('password.update');
            Route::get('/list', 'admin')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/update', 'update')->name('update');
            Route::post('/status-update', 'statusUpdate')->name('status.update');
            Route::get('/destroy/{id}', 'destroy')->name('destroy');
            Route::get('/edit/{id}', 'edit')->name('edit');
        });

        #Seller shop dashboard
        Route::get('seller/shop', [SellerShopController::class, 'index'])->name('seller.shop')->middleware('sellerMode.status.check');
        Route::post('/orders/assign', [InhouseProductOrderController::class, 'assign'])->name('order.assign');


        //Physical Inhouse Order
        Route::get('inhouse/orders/list', [InhouseProductOrderController::class, 'index'])->name('inhouse.order.index');
        Route::get('inhouse/orders/placed', [InhouseProductOrderController::class, 'placed'])->name('inhouse.order.placed');
        Route::get('inhouse/orders/return', [InhouseProductOrderController::class, 'return'])->name('inhouse.order.return');
        Route::get('inhouse/orders/pending', [InhouseProductOrderController::class, 'pending'])->name('inhouse.order.pending');
        Route::get('inhouse/orders/cancel', [InhouseProductOrderController::class, 'cancel'])->name('inhouse.order.cancel');
        Route::get('inhouse/orders/delete/{id}', [InhouseProductOrderController::class, 'delete'])->name('inhouse.order.delete');
        Route::get('inhouse/orders/confirmed', [InhouseProductOrderController::class, 'confirmed'])->name('inhouse.order.confirmed');
        Route::get('inhouse/orders/processing', [InhouseProductOrderController::class, 'processing'])->name('inhouse.order.processing');
        Route::get('inhouse/orders/shipped', [InhouseProductOrderController::class, 'shipped'])->name('inhouse.order.shipped');
        Route::get('inhouse/orders/delivered', [InhouseProductOrderController::class, 'delivered'])->name('inhouse.order.delivered');
        Route::get('inhouse/orders/cancel', [InhouseProductOrderController::class, 'cancel'])->name('inhouse.order.cancel');
        Route::get('inhouse/orders/details/{id}', [InhouseProductOrderController::class, 'details'])->name('inhouse.order.details');
        Route::get('inhouse/orders/search/{scope}', [InhouseProductOrderController::class, 'search'])->name('inhouse.order.search');
        Route::get('inhouse/orders/date/search/{scope}', [InhouseProductOrderController::class, 'dateSearch'])->name('inhouse.order.date.search');
        Route::get('inhouse/orders/invoice/{id}', [InhouseProductOrderController::class, 'invoice'])->name('inhouse.order.invoice');
        Route::get('inhouse/orders/invoice/print/{id}/{type}', [InhouseProductOrderController::class, 'printInvoice'])->name('inhouse.order.print');
        Route::post('inhouse/orders/status/update/{id?}', [InhouseProductOrderController::class, 'orderStatusUpdate'])->name('inhouse.order.status.update');
        Route::post('inhouse/orders/product/status/update', [InhouseProductOrderController::class, 'orderDetailStatusUpdate'])->name('inhouse.order.product.status.update');

        //Physical Seller Order

        Route::middleware(['sellerMode.status.check'])->group(function () {
            Route::get('seller/orders/list', [SellerProductOrderController::class, 'index'])->name('seller.order.index');
            Route::get('seller/orders/placed', [SellerProductOrderController::class, 'placed'])->name('seller.order.placed');
            Route::get('seller/orders/return', [SellerProductOrderController::class, 'return'])->name('seller.order.return');
            Route::get('seller/orders/pending', [SellerProductOrderController::class, 'pending'])->name('seller.order.pending');
            Route::get('seller/orders/cancel', [SellerProductOrderController::class, 'cancel'])->name('seller.order.cancel');
            Route::get('seller/orders/confirmed', [SellerProductOrderController::class, 'confirmed'])->name('seller.order.confirmed');
            Route::get('seller/orders/processing', [SellerProductOrderController::class, 'processing'])->name('seller.order.processing');
            Route::get('seller/orders/shipped', [SellerProductOrderController::class, 'shipped'])->name('seller.order.shipped');
            Route::get('seller/orders/delivered', [SellerProductOrderController::class, 'delivered'])->name('seller.order.delivered');
            Route::get('seller/orders/cancel', [SellerProductOrderController::class, 'cancel'])->name('seller.order.cancel');
            Route::get('seller/orders/details/{id}', [SellerProductOrderController::class, 'details'])->name('seller.order.details');
            Route::get('seller/orders/search/{scope}', [SellerProductOrderController::class, 'search'])->name('seller.order.search');
            Route::get('seller/orders/date/search/{scope}', [SellerProductOrderController::class, 'dateSearch'])->name('seller.order.date.search');
        });

        //Digital Product Inhouse and seller Order
        Route::get('digital/product/inhouse/order', [DigitalProductOrderController::class, 'inhouse'])->name('digital.order.product.inhouse');
        Route::get('digital/product/inhouse/order/search/{scope}', [DigitalProductOrderController::class, 'search'])->name('digital.order.product.inhouse.search');

        Route::get('digital/product/seller/order', [DigitalProductOrderController::class, 'seller'])->name('digital.order.product.seller')->middleware('sellerMode.status.check');;
        Route::post('digital/order/status', [DigitalProductOrderController::class, 'orderStatusUpdate'])->name('digital.order.payment.status');
        Route::get('digital/order/details/{order_id}', [DigitalProductOrderController::class, 'digitalOrderDetails'])->name('digital.order.product.details');

        //Category
        Route::get('categories', [CategoryController::class, 'index'])->name('item.category.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('item.category.create');
        Route::post('categories/store', [CategoryController::class, 'store'])->name('item.category.store');
        Route::get('categories/edit/{id}', [CategoryController::class, 'edit'])->name('item.category.edit');
        Route::post('categories/update/{id}', [CategoryController::class, 'update'])->name('item.category.update');
        // category delete
        Route::get('categories/delete/{id}', [CategoryController::class, 'delete'])->name('item.category.delete');
        Route::get('categories/top/status/{id}', [CategoryController::class, 'top'])->name('item.category.top');

        Route::get('categories/search', [CategoryController::class, 'search'])->name('item.category.search');

        Route::get('brands', [BrandController::class, 'index'])->name('item.brand.index');
        Route::get('brand/create', [BrandController::class, 'create'])->name('item.brand.create');
        Route::post('brand/store', [BrandController::class, 'store'])->name('item.brand.store');
        Route::post('brand/update', [BrandController::class, 'update'])->name('item.brand.update');
        // brand delete
        Route::get('brand/delete/{id}', [BrandController::class, 'brandDelete'])->name('item.brand.delete');
        Route::get('brand/top/status/{id}', [BrandController::class, 'top'])->name('item.brand.top');
        Route::get('brand/search', [BrandController::class, 'search'])->name('item.brand.search');




        
        //Attribute
        Route::get('attributes', [AttributeController::class, 'index'])->name('item.attribute.index');
        Route::post('attribute/store', [AttributeController::class, 'store'])->name('item.attribute.store');
        Route::post('attribute/update', [AttributeController::class, 'update'])->name('item.attribute.update');
        Route::post('attribute/value/store', [AttributeController::class, 'attributeValueStore'])->name('item.attribute.value.store');
        Route::post('attribute/value/update', [AttributeController::class, 'attributeValueUpdate'])->name('item.attribute.value.update');
        // Attribute delete
        Route::get('attribute/delete/{id}', [AttributeController::class, 'attributeDelete'])->name('item.attribute.delete');

        Route::get('attribute/values/{id}', [AttributeController::class, 'getAttributeValue'])->name('item.attribute.value.get');
        Route::get('attribute/item/value/delete/{id}', [AttributeController::class, 'attributeValueDelete'])->name('item.attribute.value.delete');

        //Product




        Route::get('inhouse/products', [ProductController::class, 'inhouseProduct'])->name('item.product.inhouse.index');


        Route::get('product/reviews/{id}', [ProductController::class, 'reviews'])->name('product.reviews');
        Route::get('product/reviews/delete/{id}', [ProductController::class, 'reviewDelete'])->name('product.review.delete');

        Route::get('inhouse/add/product', [ProductController::class, 'create'])->name('item.product.inhouse.create');

        Route::get('inhouse/product/replicate/{id}', [ProductController::class, 'replicate'])->name('item.product.inhouse.replicate');

        Route::get('inhouse/add/product', [ProductController::class, 'create'])->name('item.product.inhouse.create');
        Route::post('inhouse/product/combination', [ProductController::class, 'combination'])->name('product.combination');


        Route::post('inhouse/product/attribute', [ProductController::class, 'attrValue'])->name('product.attribute.value');
        Route::post('inhouse/product/attribute/edit', [ProductController::class, 'stock_edit'])->name('product.attribute.edit');

        Route::post('inhouse/product/store', [ProductController::class, 'store'])->name('item.product.inhouse.store');
        Route::post('inhouse/product/update/{id}', [ProductController::class, 'update'])->name('item.product.inhouse.update');
        Route::get('inhouse/product/edit/{id}', [ProductController::class, 'edit'])->name('item.product.inhouse.edit');
        Route::post('inhouse/product/delete', [ProductController::class, 'delete'])->name('item.product.inhouse.delete');
        Route::get('inhouse/product/trashed', [ProductController::class, 'trashed'])->name('item.product.inhouse.trashed');
        Route::post('inhouse/product/restore', [ProductController::class, 'restore'])->name('item.product.inhouse.restore');
        //permanent delete
        Route::post('inhouse/product/permanent-delete', [ProductController::class, 'permanentDelete'])->name('item.product.inhouse.permanentDelete');


        Route::get('inhouse/product/details/{id}', [ProductController::class, 'details'])->name('item.product.inhouse.details');
        Route::get('inhouse/order/product/{id}', [ProductController::class, 'orderItem'])->name('item.product.inhouse.order');
        Route::get('inhouse/order/placed/product/{id}', [ProductController::class, 'orderPlaced'])->name('item.product.inhouse.order.placed');
        Route::get('inhouse/order/delivered/product/{id}', [ProductController::class, 'orderDelivered'])->name('item.product.inhouse.order.delivered');
        Route::get('inhouse/product/gallery/image/delete/{id}', [ProductController::class, 'productGalleryImageDelete'])->name('item.product.inhouse.gallery.image.delete');
        Route::get('inhouse/product/search/{scope}', [ProductController::class, 'search'])->name('item.product.inhouse.search');
        Route::get('inhouse/top/product/{id}', [ProductController::class, 'top'])->name('item.product.inhouse.top');
        Route::get('inhouse/featured/product/status/update/{id}', [ProductController::class, 'featuredStatus'])->name('item.product.inhouse.featured.status');
        Route::get('inhouse/bestsellingitem/product/status/update/{id}', [ProductController::class, 'bestSellingItem'])->name('item.product.inhouse.bestsellingitem.status');

        Route::get('inhouse/suggested/product/status/update/{id}', [ProductController::class, 'suggestedItem'])->name('item.product.inhouse.suggested.status');

        //Digital Product

        Route::middleware(['sellerMode.status.check'])->group(function () {
            Route::get('digital/seller/products', [DigitalProductController::class, 'seller'])->name('digital.product.seller');
            Route::get('digital/seller/product/details/{id}', [DigitalProductController::class, 'sellerProductDetails'])->name('digital.product.seller.details');
            Route::get('digital/seller/product/attribute/value/log/{id}', [DigitalProductController::class, 'sellerProductAttributeValue'])->name('digital.product.seller.attribute.log');
            Route::get('digital/seller/product/item', [DigitalProductController::class, 'sellerProductItem'])->name('digital.product.seller.item');
            Route::post('digital/seller/product/delete', [DigitalProductController::class, 'sellerProductDelete'])->name('digital.product.seller.delete');
            Route::post('digital/seller/product/approved', [DigitalProductController::class, 'sellerProductApprovedBy'])->name('digital.product.seller.approved');
            Route::post('digital/seller/product/inactive', [DigitalProductController::class, 'sellerProductInactive'])->name('digital.product.seller.inactive');
            Route::post('digital/seller/product/restore', [DigitalProductController::class, 'sellerProductRestore'])->name('digital.product.seller.restore');
            Route::get('digital/seller/product/trashed', [DigitalProductController::class, 'sellerTrashedProduct'])->name('digital.product.seller.trashed');
        });




        Route::middleware(['sellerMode.status.check'])->group(function () {

            Route::get('seller/product/list', [SellerProductController::class, 'index'])->name('product.seller.index');
            Route::get('seller/product/new', [SellerProductController::class, 'new'])->name('product.seller.new');
            Route::get('seller/product/published', [SellerProductController::class, 'approved'])->name('product.seller.approved');
            Route::get('seller/product/refuse', [SellerProductController::class, 'refuse'])->name('product.seller.refuse');
            Route::get('seller/product/trashed', [SellerProductController::class, 'trashed'])->name('product.seller.trashed');
            Route::post('seller/product/delete', [SellerProductController::class, 'delete'])->name('product.seller.delete');
            Route::post('seller/product/approvedBy', [SellerProductController::class, 'approvedBy'])->name('product.seller.approvedby');
            Route::post('seller/product/cancelBy', [SellerProductController::class, 'cancelBy'])->name('product.seller.cancelby');
            Route::post('seller/product/restore', [SellerProductController::class, 'restore'])->name('product.seller.restore');
            Route::get('seller/product/details/{id}', [SellerProductController::class, 'details'])->name('product.seller.details');

            Route::get('seller/product/search/{scope}', [SellerProductController::class, 'search'])->name('product.seller.search');
            Route::get('seller/product/all/order/{id}', [SellerProductController::class, 'singleProductAllOrder'])->name('product.seller.all.order');
            Route::get('seller/product/placed/order/{id}', [SellerProductController::class, 'singleProductPlacedOrder'])->name('product.seller.placed.order');
            Route::get('seller/product/delivered/order/{id}', [SellerProductController::class, 'singleProductDeliveredOrder'])->name('product.seller.delivered.order');
            Route::post('seller/product/update/status', [SellerProductController::class, 'sellerProductUpdateStatus'])->name('product.seller.update.status');
        });



        #Shipping route
        Route::controller(ShippingController::class)->prefix('/shipping')->name('shipping.')->group(function () {

            #Shipping method
            Route::get('methods',  'method')->name('method.index');
            Route::post('method/store',  'methodStore')->name('method.store');
            Route::post('method/update',  'methodUpdate')->name('method.update');
            Route::get('method/search',  'searchMethod')->name('method.search');
            Route::get('method/delete/{id}',  'methodDelete')->name('method.delete');

            #Shipping configuration
            Route::get('configurations',  'configuration')->name('configuration.index');
            Route::post('configurations',  'configurationStore')->name('configuration.store');

            #Shipping Delivary
            Route::get('deliverys',  'shippingIndex')->name('delivery.index');
            Route::get('delivery/create',  'shippingCreate')->name('delivery.create');
            Route::post('delivery/store',  'shippingStore')->name('delivery.store');
            Route::get('delivery/delete/{id}',  'shippingDelete')->name('delivery.delete');
            Route::get('delivery/edit/{id}',  'shippingEdit')->name('delivery.edit');
            Route::post('delivery/update/{id}',  'shippingUpdate')->name('delivery.update');
            Route::get('search',  'search')->name('delivery.search');
        });

        Route::controller(CountryController::class)->prefix('/shipping')->name('shipping.')->group(function () {

            #Shipping country
            Route::get('countries',  'index')->name('country.index');
            Route::post('country/status',  'status')->name('country.status');
        });

        Route::controller(StateController::class)->prefix('/shipping')->name('shipping.')->group(function () {

            #Shipping state
            Route::get('state',  'index')->name('state.index');
            Route::post('state/store',  'store')->name('state.store');
            Route::post('state/status',  'status')->name('state.status');
            Route::get('state/edit/{id}',  'index')->name('state.edit');
            Route::post('state/update',  'update')->name('state.update');
            Route::get('state/destroy/{id}',  'destroy')->name('state.destroy');
        });

        Route::controller(CityController::class)->prefix('/shipping')->name('shipping.')->group(function () {

            #Shipping Cities
            Route::get('city-list',  'index')->name('city.index');
            Route::post('city/store',  'store')->name('city.store');
            Route::post('city/status',  'status')->name('city.status');
            Route::get('city/edit/{id}',  'index')->name('city.edit');
            Route::post('city/update/',  'update')->name('city.update');
            Route::get('city/destroy/{id}',  'destroy')->name('city.destroy');
        });

        Route::controller(ZoneController::class)->prefix('/shipping')->name('shipping.')->group(function () {

            #Shipping Zone
            Route::get('zone-list',  'index')->name('zone.index');
            Route::post('zone/store',  'store')->name('zone.store');
            Route::post('zone/status',  'status')->name('zone.status');
            Route::get('zone/edit/{id}',  'index')->name('zone.edit');
            Route::post('zone/update/',  'update')->name('zone.update');
            Route::get('zone/destroy/{id}',  'destroy')->name('zone.destroy');
        });



        #SEO route
        Route::controller(SeoContentController::class)->prefix('/seo')->name('seo.')->group(function () {

            Route::get('index', 'index')->name('index');
            Route::post('update', 'update')->name('update');
        });

        #Digital product route

        Route::get('digital/products', [DigitalProductController::class, 'index'])->name('digital.product.index');
        Route::get('digital/product/trashed', [DigitalProductController::class, 'trashed'])->name('digital.product.trashed');

        Route::get('digital/product/create', [DigitalProductController::class, 'create'])->name('digital.product.create');
        Route::post('digital/product/store', [DigitalProductController::class, 'store'])->name('digital.product.store');

        //digitial inhouse product delete delete
        Route::post('inhouse/digital/product/delete', [DigitalProductController::class, 'delete'])->name('digital.product.delete');
        Route::get('digital/product/edit/{id}', [DigitalProductController::class, 'edit'])->name('digital.product.edit');
        Route::post('digital/product/update/{id}', [DigitalProductController::class, 'update'])->name('digital.product.update');
        Route::post('digital/product/replicate/{id}', [DigitalProductController::class, 'replicate'])->name('digital.product.replicate');
        Route::get('digital/product/attributes/{id}', [DigitalProductController::class, 'attribute'])->name('digital.product.attribute');
        Route::post('digital/product/attributes/store', [DigitalProductController::class, 'attributeStore'])->name('digital.product.attribute.store');
        Route::get('digital/product/attribute/details/{id}', [DigitalProductController::class, 'attributeDetails'])->name('digital.product.attribute.details');
        Route::post('digital/product/attribute/update', [DigitalProductController::class, 'attributeUpdate'])->name('digital.product.attribute.update');
        Route::post('digital/product/attribute/value/delete', [DigitalProductController::class, 'attributeDelete'])->name('digital.product.attribute.value.delete');
        Route::post('digital/product/attribute/value/store/{id}', [DigitalProductController::class, 'attributeValueStore'])->name('digital.product.attribute.value.store');


        Route::get('digital/product/attribute/value/download/{id}', [DigitalProductController::class, 'attributeValueDownload'])->name('digital.product.attribute.value.download');


        Route::post('digital/product/attribute/value/update', [DigitalProductController::class, 'attributeValueUpdate'])->name('digital.product.attribute.value.update');


        Route::post('digital/product/attribute/delete', [DigitalProductController::class, 'attributeValueDelete'])->name('digital.product.attribute.delete');


        #Setting route
        Route::controller(GeneralSettingController::class)->group(function () {

            Route::get('general/setting/index', 'index')->name('general.setting.index');
            Route::get('load/wp/templates', 'loadTemplates')->name('load.templates');
            Route::post('get/wp/template', 'getTemplate')->name('get.template');
            Route::post('store/wp/template', 'storeTemplate')->name('store.template');

            Route::post('general/setting/store', 'store')->name('general.setting.store');
            Route::post('general/setting/logo/store', 'logoStore')->name('general.setting.logo.store');

            Route::get('/ai-configuration', 'aiConfiguration')->name('general.ai.configuration');
            Route::post('/ai-configuration/update', 'aiConfigurationUpdate')->name('general.ai.configuration.update');
            Route::get('general/seller/mode', 'updateSellerMode')->name('seller.mode');
            Route::get('general/debug/mode', 'updateDebugMode')->name('debug.mode');
            Route::get('general/setting/cache/clear', 'cacheClear')->name('general.setting.cache.clear');
            Route::get('system/info', 'systemInfo')->name('system.info');
            Route::get('plugin', 'plugin')->name('general.setting.plugin');
            Route::post('plugin/update', 'pluginUpdate')->name('plugin.update');
            Route::get('customer/app-onboarding/setting', 'appSettings')->name('general.app.setting');
            Route::post('customer/app-onboarding/setting/update', 'appSettingUpdate')->name('general.app.setting.update');

            Route::get('deliveryman/app-onboarding/setting', 'deliverymanAppSettings')->name('general.deliveryman.setting');
            Route::post('deliveryman/app-onboarding/setting/update', 'deliverymanAppappSettingUpdate')->name('general.deliveryman.setting.update');

            Route::get('social/login', 'socialLogin')->name('general.setting.social.login');

            Route::post('social/login/update', 'socialLoginUpdate')->name('social.login.update');

            #kyc config
            Route::get('/vendor/kyc-configuration', 'kycConfig')->name('general.setting.kyc');
            Route::post('/kyc-configuration/store', 'kycSetting')->name('general.setting.kyc.store');
        });


        #Delevary man
        Route::controller(DeliveryManController::class)->prefix('/delivery-man')->name('delivery-man.')->group(function () {
            Route::get('list', 'list')->name('list');
            
            Route::get('create', 'create')->name('create');
            Route::post('order/cash-collect', 'cashCollect')->name('cash.collect');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::get('overview/{id}', 'overview')->name('overview');
            Route::get('earning/{id}', 'earning')->name('earning');
            Route::post('/status-update', 'statusUpdate')->name('status.update');
            Route::post('/kyc-status-update', 'kycStatusUpdate')->name('kyc.status.update');

            Route::post('/password-update', 'passwordUpdate')->name('password.update');
            Route::post('update', 'update')->name('update');
            Route::get('delete/{id}', 'delete')->name('delete');
            Route::get('order/list/{id}', 'orders')->name('order.list');
            Route::get('rating/list/{id}', 'ratings')->name('rating.list');
            Route::get('configuration', 'configuration')->name('configuration');
            Route::get('/kyc-configuration', 'kycConfiguration')->name('kyc.configuration');
            Route::post('/kyc-configuration/update', 'kycConfigurationUpdate')->name('kyc.configuration.update');

            Route::get('reward/log', 'rewards')->name('rewards');
            Route::get('referral/log', 'referralLogs')->name(name: 'referral');

            Route::post('/balance/update', 'balanceUpdate')->name('balance.update');



        });


        #Tax route
        Route::controller(TaxController::class)->prefix('/tax')->name('tax.')->group(function () {
            Route::get('list', 'index')->name('list');
            Route::post('store', 'store')->name('store');
            Route::post('status-update', 'statusUpdate')->name('status.update');
            Route::post('update', 'update')->name('update');
            Route::get('delete/{id}', 'delete')->name('delete');
        });


        # Addon Route
        Route::controller(AddonManagerController::class)->prefix('/addon')
            ->name('addon.')->group(function () {

                Route::get('manager', 'manager')->name('manager');
                Route::post('store', 'store')->name('store');
            });


        #Currency Route
        Route::controller(CurrencyController::class)->prefix('/general/setting/currenciy')
            ->name('general.setting.currency.')->group(function () {

                Route::get('index', 'index')->name('index');
                Route::get('/default/{id}', 'default')->name('default');
                Route::post('/status-update', 'statusUpdate')->name('status.update');
                Route::post('/store', 'store')->name('store');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/update', 'update')->name('update');
            });


        #Customer  Route
        Route::controller(CustomerController::class)
            ->name('customer.')->group(function () {

                Route::get('customers', 'index')->name('index');
                Route::get('customer/active', 'active')->name('active');
                Route::get('customer/banned', 'banned')->name('banned');
                Route::get('customer/detail/{id}', 'details')->name('details');
                Route::get('customer/login/{id}', 'login')->name('login');
                Route::get('customer/create', 'create')->name('create');
                Route::post('customer/store', 'store')->name('store');
                Route::post('customer/update/{id}', 'update')->name('update');
                Route::get('customer/search', 'search')->name('search');

                Route::get('customer/delete/{id}', 'delete')->name('delete');


                Route::post('customer/balance/update', 'balanceUpdate')->name('balance.update');
                Route::get('customer/rewards', 'rewards')->name('rewards');


                Route::get('customer/transaction/log/{id}', 'transaction')->name('transaction');
                Route::get('customer/physical/product/order/{id}', 'physicalProductOrder')
                    ->name('physical.product.order');
                Route::get('customer/digital/product/order/{id}', 'digitalProductOrder')
                    ->name('digital.product.order');




                    
            });


        //news latter start
        Route::get('news-letter/index', [NewsLatterController::class, 'index'])->name('newsLatter.index');
        Route::post('news-latter/update', [NewsLatterController::class, 'update'])->name('newsLatter.update');


        #Flash deal Route
        Route::controller(PromoteController::class)->name('promote.flash.deals.')->prefix('flash/deals')->group(function () {

            Route::get('/index', 'flashDeals')->name('index');
            Route::get('/create', 'flashDealCreate')->name('create');
            Route::get('/edit/{id}', 'flashDealEdit')->name('edit');
            Route::post('/store', 'flashDealStore')->name('store');
            Route::post('/update', 'flashDealUpdate')->name('update');
            Route::any('/delete/{id}', 'flashDealDelete')->name('delete');
            Route::any('/status/update', 'flashDealStatusUpdate')->name('status.update');
        });


        #Faq route
        Route::controller(FaqController::class)->name('faq.')->prefix('/faq')->group(function () {

            Route::get('/index', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/update', 'update')->name('update');
            Route::get('/delete/{id}', 'delete')->name('delete');
        });


        //Coupon Route
        Route::controller(CouponController::class)->name('promote.coupon.')->prefix('/coupon')->group(function () {
            Route::get('index',  'index')->name('index');
            Route::get('create',  'create')->name('create');
            Route::post('store',  'store')->name('store');
            Route::get('edit/{id}',  'edit')->name('edit');
            Route::post('update/{id}',  'update')->name('update');
            Route::post('delete',  'couponDelete')->name('delete');
        });



        #Pricing plan Route
        Route::controller(PricingPlanController::class)->name('plan.')->prefix('/plan')->group(function () {

            Route::get('index', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::post('update', 'update')->name('update');
            Route::get('search', 'searchPlan')->name('search');
            Route::get('/delete/{id}', 'delete')->name('delete');

            #Subscription Route
            Route::get('/subscription', 'subscription')->name('subscription');
            Route::get('/subscription/search', 'search')->name('subscription.search');
            Route::post('/subscription/approved', 'subscriptionApproved')->name('subscription.approved');
        });


        #Seller module
        Route::middleware(['sellerMode.status.check'])->group(function () {


            #Seller route
            Route::controller(SellerController::class)->group(function () {

                Route::get('sellers/index', 'index')->name('seller.info.index');
                Route::get('sellers/active', 'active')->name('seller.info.active');
                Route::get('sellers/banned', 'banned')->name('seller.info.banned');
                Route::get('seller/shop/{id}', 'shop')->name('seller.info.shop');

                Route::get('seller/create', 'create')->name('seller.info.create');
                Route::post('seller/store', 'store')->name('seller.info.store');

                Route::get('seller/delete/{id}', 'delete')->name('seller.info.delete');

                
                Route::post('seller/shop/update/{id}', 'shopUpdate')->name('seller.info.shop.update');
                Route::get('seller/detail/{id}', 'details')->name('seller.info.details');
                Route::get('seller/login/{id}', 'login')->name('seller.info.login');
                Route::post('seller/detail/update/{id}', 'update')->name('seller.info.details.update');
                Route::get('seller/physical/products/{id}', 'sellerAllProduct')->name('seller.info.physical.product');
                Route::get('seller/all/transaction/log/{id}', 'sellerTransaction')->name('seller.info.transaction.log');
                Route::get('seller/all/withdraw/log/{id}', 'sellerWithdraw')->name('seller.info.withdraw.log');
                Route::get('seller/digital/products/{id}', 'sellerAllDigitalProduct')->name('seller.info.digital.product');
                Route::get('seller/digital/product/orders/{id}', 'sellerDigitalProductOrder')->name('seller.info.digital.product.order');
                Route::get('seller/physical/product/orders/{id}', 'sellerPhysicalProductOrder')->name('seller.info.physical.product.order');
                Route::get('seller/support/ticket/{id}', 'ticket')->name('seller.info.support.ticket');
                Route::get('seller/support/search/{scope}', 'search')->name('seller.info.search');
                Route::get('seller/info/best/status/{id}', 'bestSeller')->name('seller.info.best.status');
                Route::post('seller/balance/update', 'sellerBalanceUpdate')->name('seller.balance.update');
            });
        });



        #Mail Category
        Route::controller(MenuController::class)->group(function () {

            Route::get('menu/index', 'index')->name('menu.index');
            Route::get('menu/create', 'create')->name('menu.create');
            Route::get('home/category', 'homeCategory')->name('home.category');
            Route::get('menu/edit/{id}', 'edit')->name('menu.edit');
            Route::post('menu/store', 'store')->name('menu.store');
            Route::get('menu/delete/{id}', 'delete')->name('menu.delete');
            Route::post('menu/update/{id}', 'update')->name('menu.update');
            Route::post('home/categories/update', 'homeCategoryUpdate')->name('home.category.update');
        });


        #Mail Configuration route
        Route::controller(MailConfigurationController::class)->group(function () {

            Route::get('mail/configuration', 'index')->name('mail.configuration');
            Route::post('mail/update/{id}', 'mailUpdate')->name('mail.update');
            Route::get('mail/edit/{id}', 'edit')->name('mail.edit');
            Route::post('mail/send/method', 'sendMailMethod')->name('mail.send.method');
            Route::get('global/template',  'globalTemplate')->name('mail.global.template');
            Route::post('global/template/update', 'globalTemplateUpdate')->name('global.template.update');
        });

        #Mail Template route
        Route::controller(EmailTemplateController::class)->name('notification.templates.')->prefix('/notification')->group(function () {

            Route::get('templates', 'index')->name('index');
            Route::get('template/edit/{id}', 'edit')->name('edit');
            Route::post('template/update/{id}', 'update')->name('update');
        });


        #SMS Gateway route
        Route::controller(SmsGatewayController::class)->name('sms.')->prefix('/sms')->group(function () {

            Route::get('gateway', 'index')->name('gateway.index');
            Route::get('gateway/edit/{id}', 'edit')->name('gateway.edit');
            Route::post('gateway/update/{id}', 'update')->name('gateway.update');
            Route::post('default/gateway', 'defaultGateway')->name('default.gateway');
            Route::get('global/template', 'globalSMSTemplate')->name('global.template');
            Route::post('global/template/store', 'globalSMSTemplateStore')->name('global.template.store');
        });



        #SMS Template route
        Route::controller(SmsTemplateController::class)->name('sms.template.')->prefix('/sms')->group(function () {
            Route::get('templates', 'index')->name('index');
            Route::get('template/edit/{id}', 'edit')->name('edit');
            Route::post('template/update/{id}', 'update')->name('update');
        });


        #Contact Route
        Route::controller(ContactUsController::class)->name('contact.')->prefix('/contact')->group(function () {

            Route::get('/index', 'index')->name('index');
            Route::get('/show/{id}', 'show')->name('show');
            Route::get('/destroy/{id}', 'destroy')->name('destory');
            Route::post('/send/mail', 'sendMail')->name('send.mail');
        });


        #Support ticket Route
        Route::controller(SupportTicketController::class)->name('support.ticket.')->prefix('/support/ticket/')->group(function () {

            Route::get('index', 'index')->name('index');
            Route::post('reply/{id}', 'ticketReply')->name('reply');
            Route::post('closed/{id}', 'closedTicket')->name('closeds');
            Route::get('tickets', 'running')->name('running');
            Route::get('replied', 'replied')->name('replied');
            Route::get('answered', 'answered')->name('answered');
            Route::get('closeds', 'closed')->name('closed');
            Route::get('details/{id}', 'ticketDetails')->name('details');
            Route::get('download/{id}', 'supportTicketDownlode')->name('download');
        });

        #Withdraw Route
        Route::controller(WithdrawController::class)->name('withdraw.log.')->prefix('/withdraw')->group(function () {

            Route::get('/details/{id}',  'detail')->name('details');
            Route::get('/logs',  'index')->name('index');
            Route::get('/pending/logs',  'pending')->name('pending');
            Route::get('/approved/logs',  'approved')->name('approved');
            Route::get('/rejected/logs',  'rejected')->name('rejected');
            Route::post('/approvedby',  'approvedBy')->name('approvedby');
            Route::post('/rejectedby',  'rejectedBy')->name('rejectedby');
        });


        #Withdraw Method Route
        Route::controller(WithdrawMethodController::class)->name('withdraw.method.')->prefix('/withdraw/methods/')->group(function () {
            Route::get('index',  'index')->name('index');
            Route::get('create',  'create')->name('create');
            Route::post('store',  'store')->name('store');
            Route::get('edit/{id}',  'edit')->name('edit');
            Route::post('update/{id}',  'update')->name('update');
            Route::get('delete/{id}',  'delete')->name('delete');
        });


        #Payment Log Route
        Route::controller(PaymentLogController::class)->name('payment.')->prefix('/payment')->group(function () {
            Route::get('/logs',  'index')->name('index');
            Route::get('/pending/logs', 'pending')->name('pending');
            Route::get('/approved/logs', 'approved')->name('approved');
            Route::get('/rejected/logs', 'rejected')->name('rejected');
        });


        #Deposit Log Route
        Route::controller(DepositController::class)->name('deposit.')->prefix('/deposit')->group(function () {
            Route::get('/logs',  'index')->name('index');
            Route::get('/pending/logs', 'pending')->name('pending');
            Route::get('/approved/logs', 'approved')->name('approved');
            Route::get('/rejected/logs', 'rejected')->name('rejected');
            Route::get('/show/{id}', 'show')->name('show');
            Route::post('/update',  'update')->name('update');
        });



        #Payment method Route
        Route::controller(PaymentMethodController::class)->name('gateway.payment.')->prefix('/payment')->group(function () {

            Route::get('methods', 'index')->name('method');
            Route::get('manual/methods', 'index')->name('manual.method');
            Route::get('manual/method/create', 'create')->name('manual.method.create');
            Route::post('manual/method/store', 'store')->name('manual.method.store');
            Route::get('delete/{id}',  'delete')->name('delete');
            Route::post('update/{id}', 'update')->name('update');
            Route::get('method/edit/{slug}/{id}', 'edit')->name('edit');
            Route::get('search', 'search')->name('search');

        });



        #Transaction Route
        Route::controller(ReportController::class)->name('report.')->prefix('/report')->group(function () {
            Route::get('/user/transactions',  'userTransaction')->name('user.transaction');
            Route::get('/deliveryman/transactions',  'deliverymanTransaction')->name('deliveryman.transaction');
            Route::get('/guest/transactions',  'guestTransaction')->name('guest.transaction');
            Route::get('/seller/transactions', 'sellerTransaction')->name('seller.transaction')->middleware('sellerMode.status.check');
            Route::get('/kyc/logs',  'kycLogs')->name('kyc.log');
            Route::get('/kyc/log/show/{id}',  'kycLogShow')->name('kyc.log.show');
            Route::post('/kyc/log/update',  'kycUpdate')->name('kyc.log.update');
        });



        #Frontend route
        Route::controller(FrontendController::class)->name('frontend.')->prefix('/frontend')->group(function () {

            #Banner route
            Route::get('promotional/banner', 'promotionalBanner')->name('promotional.banner');
            Route::get('section/banner', 'banner')->name('section.banner');
            Route::get('section/banner/create', 'bannerCreate')->name('section.banner.create');
            Route::get('section/banner/edit/{id}', 'bannerEdit')->name('section.banner.edit');
            Route::post('section/banner/store', 'bannerStore')->name('section.banner.store');
            Route::post('section/banner/update', 'bannerUpdate')->name('section.banner.update');
            Route::get('section/banner/delete/{id}', 'bannerDelete')->name('section.banner.delete');
            Route::post('section/banner/status', 'bannerStatus')->name('section.banner.status');

            #Section route
            Route::get('section/', 'frontend')->name('section');
            Route::post('section/{id}', 'frontendUpdate')->name('section.update');
            Route::get('new/element/{slug}/{id}', 'frontendElementAdd')->name('new.element');
            Route::get('data/{slug}/{id}', 'frontendElement')->name('data');
            Route::post('element/store/{id}', 'frontendElementStore')->name('element.store');
            Route::post('element/update/{id}', 'frontendElementUpdate')->name('element.update');

            #testimonial route
            Route::get('testimonial/index', 'testimonial')->name('testimonial.index');
            Route::post('testimonial/store', 'testimonialStore')->name('testimonial.store');
            Route::post('testimonial/update', 'testimonialUpdate')->name('testimonial.update');
            Route::post('testimonial/status/update', 'testimonialStatusUpdate')->name('testimonial.status.update');
            Route::get('testimonial/delete/{id}', 'testimonialDelete')->name('testimonial.delete');
        });



        #Subscription route
        Route::controller(SubscriberController::class)->prefix("/subscriber")->name('subscriber.')->group(function () {

            Route::get('index', 'index')->name('index');
            Route::get('send/mail', 'sendMail')->name('send.mail');
            Route::get('delete/{id}', 'delete')->name('delete');
            Route::post('send/mail/submit', 'sendEmailSubscriber')->name('send.mail.submit');
        });



        #Language route
        Route::controller(LanguageController::class)->prefix("/language")->name('language.')->group(function () {

            Route::get('/list', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/status-update', 'statusUpdate')->name('status.update');
            Route::get('/make/default/{id}', 'setDefaultLang')->name('make.default');
            Route::get('/destroy/{id}', 'destroy')->name('destroy');
            Route::get('translate/{code}', 'translate')->name('translate');
            Route::post('translate-key', 'tranlateKey')->name('tranlateKey');
            Route::get('destroy/translate-key/{id}', 'destroyTranslateKey')->name('destroy.key');
        });


        #Roles route
        Route::controller(RolesController::class)->prefix("/roles")->name('role.')->group(function () {

            Route::get('/list', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/update', 'update')->name('update');
            Route::post('/status-update', 'statusUpdate')->name('status.update');
            Route::get('/destroy/{id}', 'destroy')->name('destroy');
            Route::get('/edit/{id}', 'edit')->name('edit');
        });


        #Pages route
        Route::controller(PageSetUpController::class)->prefix("/page")->name('page.')->group(function () {

            Route::get('page/setups', 'index')->name('index');
            Route::get('page/setup/create', 'create')->name('create');
            Route::post('page/setup/store', 'store')->name('store');
            Route::get('page/setup/edit/{slug}/{id}', 'edit')->name('edit');
            Route::post('page/setup/update/{id}', 'update')->name('update');
            Route::get('page/setup/delete/{id}', 'delete')->name('delete');
            Route::post('/status-update', 'statusUpdate')->name('status.update');
        });


        #Blog route
        Route::controller(BlogController::class)->prefix("/blog")->name('blog.')->group(function () {

            Route::get('index',  'index')->name('index');
            Route::get('create',  'create')->name('create');
            Route::post('store',  'store')->name('store');
            Route::get('edit/{slug}/{id}',  'edit')->name('edit');
            Route::post('update/{id}',  'update')->name('update');
            Route::get('show/{id}',  'show')->name('show');
            Route::get('delete/{id}',  'delete')->name('delete');
            Route::post('status-update', 'statusUpdate')->name('status.update');
            Route::get('search',  'search')->name('search');
        });


        #Campaign route
        Route::controller(CampaignController::class)->prefix('campaign')->name('campaign.')->group(function () {
            Route::get('/list', 'index')->name('index');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/update', 'update')->name('update');
        });


        #security section
        Route::controller(SecurityController::class)->prefix('security/')->name('security.')->group(function () {

            #ip section
            Route::prefix("/ip")->name('ip.')->group(function () {
                Route::get('/list', 'ipList')->name('list');
                Route::post('/store', 'ipStore')->name('store');
                Route::post('/status-update', 'ipStatus')->name('update.status');
                Route::get('/destroy/{id}', 'ipDestroy')->name('destroy');
            });

            #dos security
            Route::get('/dos', 'dos')->name('dos');
            Route::post('/dos/update', 'dosUpdate')->name('dos.update');
        });
    });


    Route::controller(SystemUpdateController::class)->group(function () {

        /** file upload update */
        Route::get('/system-update', "init")->name('system.update.init');
        Route::post('/system/update', "update")->name('system.update');


        /** manual update */

        Route::get('/manual/update', "manualUpdate")->name('manual.update')->withoutMiddleware(ManualUpdateMiddleware::class);
        Route::get('/manual/update-application', "manualUpdateApplication")->name('manual.update.application')->withoutMiddleware(ManualUpdateMiddleware::class);
    });
});
