<?php

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;

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

/**
 * Authenticaed User for VUEX
 */
Route::get('/auth_user', [UserController::class, 'getAuthenticatedUser'])->name('get.auth.user');
Auth::routes([
    'register' => false, // Registration Routes...
    // 'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
    // 'login' => false, // Login Routes...
]);

/**
 * Login with Sanctum route
 */
// Route::get('/login', function (){
//     return view('login');
// })->name('sanctum.login');

Route::get('/', [DashboardController::class, 'home'])->name('home');
Route::get('/home', [DashboardController::class, 'home'])->name('home');

/**
 * Search UIem
 */
Route::post('/item/search', [ItemController::class, 'searchItem'])->name('search.item');

Route::group(['prefix'=>'d','as'=>'dashboard.', 'middleware' => 'auth'], function(){

    /**
     * vue-router pages
     */
    Route::get('/', [DashboardController::class, 'dashboard']);
    Route::get('/{page}', [DashboardController::class, 'dashboard']);
    Route::get('/{page}/{action}', [DashboardController::class, 'dashboard']);

    /**
     * Users
     */
    Route::get('/user/edit/{id}', [DashboardController::class, 'dashboard'])->name('user.edit');
    Route::get('/user/get/all', [UserController::class, 'getAllUsers'])->name('user.get.all');
    Route::get('/user/get/{id}', [UserController::class, 'getSingleUser'])->name('user.get.single');
    Route::post('/user/status/update', [UserController::class, 'updateUserStatus'])->name('user.status.update');
    Route::post('/user/save', [UserController::class, 'saveUserData'])->name('user.save.data');
    // Route::post('/user/check/email', [UserController::class, 'checkEmail'])->name('user.check.email');
    // Route::post('/user/check/username', [UserController::class, 'checkUsername'])->name('user.check.username');


    /**
     * Profiles
     */
    Route::post('/profile/save', [ProfileController::class, 'saveProfile'])->name('user.profile');

    /**
     * Items
     */
    Route::get('/items/fetch/all', [ItemController::class, 'getAllItems'])->name('item.get.all');

    Route::post('/item/save', [ItemController::class, 'saveItem'])->name('item.save');
    Route::get('/items/get/all', [ItemController::class, 'getPaginatedItems'])->name('item.get.paginated');
    Route::get('/items/page/{page}', [DashboardController::class, 'dashboard'])->name('item.paginated');
    Route::get('/items/import', [DashboardController::class, 'dashboard'])->name('item.import');
    Route::post('/import/items', [ItemController::class, 'importItems'])->name('item.save.import');

    /**
     * Locations
     */
    Route::get('/locations/fetch/all', [LocationController::class, 'getAllLocations'])->name('location.get.all');

    /**
     * Sales Representatives
     */
    Route::get('/salesrep/fetch/all', [UserController::class, 'getAllSalesRepresentatives'])->name('user.get.all.sales.representatives');

    Route::post('/location/save', [LocationController::class, 'saveLocation'])->name('location.save');
    Route::get('/customers/get/all', [LocationController::class, 'getPaginatedLocations'])->name('location.get.paginated');
    Route::get('/customers/page/{page}', [DashboardController::class, 'dashboard'])->name('location.paginated');
    Route::get('/customers/import', [DashboardController::class, 'dashboard'])->name('location.import');
    Route::post('/import/customers', [LocationController::class, 'importLocations'])->name('location.save.import');

    /**
     * Orders
     */
    Route::get('/orders/{status?}', [DashboardController::class, 'dashboard'])->name('orders');
    Route::post('/orders/get/paginated/{status}/{filter_data}', [OrderController::class, 'getFilteredOrdersForAdmin'])->name('orders');


    // Route::get('/orders/page/{page}', [DashboardController::class, 'dashboard'])->name('order.paginated');
    Route::get('/orders', [DashboardController::class, 'dashboard'])->name('orders');
    Route::get('/orders/{status?}/page/{page}', [DashboardController::class, 'dashboard'])->name('order.page.paginated');
    Route::get('/orders/get/paginated/{status?}', [OrderController::class, 'getPaginatedOrdersForAdmin'])->name('orders.get.paginated');
});



/**
 * Staff Routes
 */
Route::group(['prefix'=>'staff','as'=>'staff.', 'middleware' => 'auth'], function(){
    /**
     * vue-router pages
     */
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/{page}', [DashboardController::class, 'dashboard'])->name('page');
    Route::get('/{page}/{action}', [DashboardController::class, 'dashboard'])->name('page.action');

    /**
     * Orders
     */
    Route::post('/order/save/detail', [OrderController::class, 'addOrderDetail'])->name('order.add.order.detail');
    Route::post('/order/create', [OrderController::class, 'createOrder'])->name('order.create');
    Route::get('/order/get/{ordernum}', [OrderController::class, 'getSingleOrder'])->name('order.get.single.order');
    Route::get('/order/edit/{ordernum}', [DashboardController::class, 'dashboard'])->name('order.edit');
    // Route::get('/order-form', [DashboardController::class, 'dashboard'])->name('order.form');
    Route::get('/orders/{status?}', [DashboardController::class, 'dashboard'])->name('orders');
    Route::get('/orders/{status?}/page/{page}', [DashboardController::class, 'dashboard'])->name('order.paginated');
});
Route::post('/d/user/changepassword', [UserController::class, 'changePassword'])->middleware('auth')->name('user.change.password');

Route::get('/orders/get/paginated/{status?}', [OrderController::class, 'getPaginatedOrders'])->middleware('auth')->name('order.get.paginated');


Route::post('/order/detail/remove', [OrderController::class, 'removeOrderDetail'])->middleware('auth')->name('order.remove.item');
Route::post('/order/update', [OrderController::class, 'updateOrder'])->middleware('auth')->name('order.update');
Route::post('/order/update-erp', [OrderController::class, 'updateERPOrder'])->middleware('auth')->name('order.update.erp');

Route::get('/user/account-settings',  [DashboardController::class, 'dashboard'])->name('user.account.settings')->middleware('auth');
Route::get('/notfound',  [DashboardController::class, 'dashboard'])->name('page.notfound')->middleware('auth');
Route::get('/forbidden',  [DashboardController::class, 'dashboard'])->name('page.forbidden')->middleware('auth');

/**
 * Files
 */
Route::get('/file/{path}',  [FileController::class, 'showFile'])->name('file.show')->middleware('auth');


/**
 * Email View Test
 */
// Route::get('/order-notification', function (){
//     $adminAccounts = User::where(['role' => 'admin', 'status' => 'active'])->get();
//     $theOrder = Order::where('order_number', '2-260822-6')->firstOrFail();

//     return view('emails.order-submission-mail', compact('adminAccounts', 'theOrder'));
// });
