<?php
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\AdvertController;
use App\Http\Controllers\Api\v1\ExchangeRateController;
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



Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    //create Super Admin account
    Route::post('/register', [AuthController::class, 'createAccount']);

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('/valueMoney', [ExchangeRateController::class, 'valueMoney']);
        //Advert Booking routes
        Route::post('/bookAdvert', [AdvertController::class, 'bookAdvert']);
        Route::get('/getBookedAdvert', [AdvertController::class, 'getBookedAdvert']);
        Route::get('/getUserBookings', [AdvertController::class, 'getUserBookings']);
        Route::get('/getBookingsByStatus', [AdvertController::class, 'getBookingsByStatus']);
        Route::post('/changeBookingStatus', [AdvertController::class, 'changeBookingStatus']);
        Route::post('/confirmPrice', [AdvertController::class, 'confirmPrice']);

        // routes accessible only to SuperAdmin
        Route::group(['middleware' => 'superadmin'], function () {
            //user/admin registration
            Route::post('/createAdminUser', [AuthController::class, 'createAdminUser']);
        });

        // routes accessible only to Admin and SuperAdmin
        Route::group(['middleware' => 'teamlead'], function () {
            //
        });

        Route::group(['middleware' => 'staff'], function () {
            //
        });
    });
});
