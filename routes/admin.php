<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAccountsController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\BusinessController;
// use App\Http\Controllers\Admin\PlansController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// }); 
Route::group(['middleware' => 'auth:sanctum'], function(){
    //All secure URL's
    Route::post("master",[MasterController::class,'master']);
    Route::post("link",[MasterController::class,'link']);
    Route::post("business/list",[BusinessController::class,'business']);
    // Route::post("checklist",[AdminController::class,'checklist']);

    // Route::post("clients",[ClientsController::class,'clients']);

    // Route::post("plans",[PlansController::class,'plans']);
    });
Route::post("master/whitelabel",[MasterController::class,'whitelabel']);
Route::post("accounts/login",[AdminAccountsController::class,'login']);
// Route::get("Accounts/register", [AccountController::class, 'Register']);