<?php

use App\Http\Controllers\InstallerController;

use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\ManualUpdateMiddleware;
use App\Http\Middleware\PurchaseValidation;
use App\Http\Middleware\SoftwareVerification;

use Illuminate\Support\Facades\Route;


    #Install route
    Route::controller(InstallerController::class)->prefix("/install")->name('install.')
     ->middleware(['sanitizer'])
     ->withoutMiddleware([SoftwareVerification::class,LanguageMiddleware::class,ManualUpdateMiddleware::class])->group(function(){
        Route::get('/','init')->name('init');
        Route::get('/requirement-verification','requirementVerification')->name('requirement.verification');
        Route::get('/envato-verification','envatoVerification')->name('envato.verification');
        Route::post('/purchase-code/verification','codeVerification')->name('purchase.code.verification');
        Route::get('/db-setup','dbSetup')->name('db.setup');
        Route::post('/db-store','dbStore')->name('db.store');
        Route::get('/import-database','dbImport')->name('db.import');
        Route::any('/import-database/store','dbImportStore')->name('db.import.store');
        Route::get('setup-finished','setupFinished')->name('setup.finished');

   });

   Route::get('invalid-license',[InstallerController::class ,'invalidPuchase'])->name('invalid.puchase')->middleware(['sanitizer'])
   ->withoutMiddleware([LanguageMiddleware::class]);

   Route::post('verify-puchase',[InstallerController::class ,'verifyPuchase'])->name('verify.puchase')->middleware(['sanitizer'])
   ->withoutMiddleware([LanguageMiddleware::class]);

   Route::fallback(function() {
     abort(404,'Not found'); 
  });


