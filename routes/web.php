<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function (Request  $request) {
//    $id = @$request->id ? $request->id : 0;
//    $user = Auth::user();
//    $ctUser =
//    return view('layouts.app', compact('id'));
//})->middleware(['auth.shopify', 'billable'])->name('home');

Route::get('/', 'Dashboard\DashboardController@appBladeindex')->middleware(['auth.shopify', 'billable'])->name('home');

Route::post(
    '/webhook/app-uninstalled', function(){
    \App\Jobs\AppUninstalledJob::dispatchNow();
});
Route::get(
    '/login', 'Auth\AuthController@index'
)->name('login');

Route::group(['middleware' => ['auth.shopify', 'billable']], function () {
    Route::get('get-plan', 'Dashboard\DashboardController@getPlan')->name('get-plan');
    Route::get('get-trial', 'Dashboard\DashboardController@getTrial')->name('get-trial');

    // dashboard routes
    Route::get('dashboard', 'Dashboard\DashboardController@index')->name('dashboard');
    Route::get('dashboard-filter', 'Dashboard\DashboardController@filterChart')->name('dashboard-filter');

    // order details routes
    Route::get('get-order', 'Order\OrderController@index')->name('get-order');
    Route::get('change-status', 'Order\OrderController@changeStatus')->name('change-status');

    // order listing routes

    Route::get('get-order-list', 'Order\OrderController@orderList')->name('get-order-list');

    // setting routes
    Route::get('settings', 'Setting\SettingController@index')->name('settings');
    Route::post('settings', 'Setting\SettingController@store')->name('settings');

    // threat intelligence
    Route::post('get-threat-intelligence', 'Threat\ThreatController@index')->name('get-threat-intelligence');
});

Route::get('/test', 'Test\TestController@index')->middleware(['auth.shopify', 'billable'])->name('test');

Route::get('flush', function(){
    request()->session()->flush();
});
