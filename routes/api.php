<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

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

Route::post('login', [Api\AuthController::class, 'login']);
Route::post('register', [Api\RegisterController::class, 'register']);
Route::post('forgot', [Api\ForgotController::class, 'forgot']);
Route::post('reset', [Api\ForgotController::class, 'reset']);
Route::get('email/resend/{user}', [Api\VerifyController::class, 'resend'])->name('verification.resend');
Route::get('email/verify/{id}', [Api\VerifyController::class, 'verify'])->name('verification.verify');; // Make sure to keep this as your route name
    
Route::group(['middleware' => ['auth:api'], 'namespace'=>'App\Http\Controllers\Api'], function () {
    Route::get('user', [Api\AuthController::class, 'user']);

    /**
     * Transaksi
     */

    // Perjalan Dinas
    Route::apiResource('/business-trips', 'BusinessTripApplicationController');
    Route::get('/business-trip-list', 'BusinessTripApplicationController@loadList');
    Route::post('/business-trip-cancel/{id}', 'BusinessTripApplicationController@cancel');
    Route::post('/business-trip-approval/{id}', 'BusinessTripApplicationController@approval');

    // Peminjaman Mobil
    Route::apiResource('/vehicle-loans', 'VehicleLoanController');
    Route::get('/vehicle-loan-list', 'VehicleLoanController@loadList');
    Route::post('/vehicle-loan-cancel/{id}', 'VehicleLoanController@cancel');
    Route::post('/vehicle-loan-approval/{id}', 'VehicleLoanController@approval');

    // Uang Muka
    Route::apiResource('/down-payment-requests', 'DownPaymentRequestController');
    Route::get('/down-payment-request-list', 'DownPaymentRequestController@loadList');
    Route::post('/down-payment-request-cancel/{id}', 'DownPaymentRequestController@cancel');
    Route::post('/down-payment-request-approval/{id}', 'DownPaymentRequestController@approval');

    /**
     * User Manajemen
     */
    
    //Hak Akses
    Route::apiResource('/roles', 'RoleController');
    Route::get('/role-list', 'RoleController@loadList');
    // User
    Route::apiResource('/mst-users', 'UserController');
    Route::get('/mst-user-list', 'UserController@loadList');

    /** 
     * Data Master
     */

    // Jabatan
    Route::apiResource('/job-positions', 'JobPositionController');
    Route::get('/job-position-list', 'JobPositionController@loadList');
    // Kategori Pekerjaan
    Route::apiResource('/job-categories', 'JobCategoryController');
    Route::get('/job-category-list', 'JobCategoryController@loadList');
    // Kategori Biaya
    Route::apiResource('/cost-categories', 'CostCategoryController');
    Route::get('/cost-category-list', 'CostCategoryController@loadList');
    // Kendaraan
    Route::apiResource('/vehicles', 'VehicleController');
    Route::get('/vehicle-list', 'VehicleController@loadList');
    // Customer
    Route::apiResource('/customers', 'CustomerController');
    Route::get('/customer-list', 'CustomerController@loadList');
});

// PDF
Route::get('/business-trip-letter/{id}', [Api\BusinessTripApplicationController::class, 'letter']);