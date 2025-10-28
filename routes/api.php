<?php


use Illuminate\Support\Facades\Route;




Route::get('test', function () {
    return response()->json([
        'status' => true,
        'message' => 'API working fine'
    ]);
});


// protected route
Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'API\V1'], function () {

  // Public
  Route::controller('RegisterController')->group(function () {
    Route::post('resgister', 'register');
    Route::post('login', 'login');
  });

  // With Auth
  Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('category', 'CategoryController');
    Route::resource('brand', 'BrandController');

    Route::resource('products', 'ProductController');
    Route::get('/Products/list', 'ProductController@list');
    Route::post('UpdateProducts/{id}', 'ProductController@update');
    Route::get('get_scheme_product', 'ProductController@get_scheme_product');
    Route::get('get_all_scheme_product', 'ProductController@get_all_scheme_product');
 Route::get('all_scheme_product', 'ProductController@all_scheme_product');

    Route::controller('AttendenceController')->prefix('attendence')->group(function () {
      Route::post('in', 'in');
      Route::post('out', 'out');
      Route::get('getAttendenceList', 'getAttendenceList');
    });

    Route::controller('ShopController')->prefix('shop')->group(function () {
      Route::post('addShop', 'addShop')->middleware('check_route');
      Route::get('userWiseShopList', 'userWiseShopList');
      Route::post('visitShopAdd', 'visitShopAdd');
      Route::get('visitShopList', 'visitShopList');
      Route::get('shopTypeList', 'shopTypeList');
      Route::put('updateCordinates/{id}', 'updateCordinates');

      Route::get('shop_wise_sales_report', 'shop_wise_sales_report');
      Route::get('order_booker_daily_activity_location_report', 'order_booker_daily_activity_location_report');
      Route::get('order_booker_daily_activity_location_report_new', 'order_booker_daily_activity_location_report_new');

    });




    Route::controller('SalesController')->prefix('sales')->group(function () {
      Route::post('orderCreate', 'orderCreate');
 Route::post('orderCreateNew', 'orderCreateNew');
      Route::post('orderUpdate/{id}', 'orderUpdate');
      Route::get('orderList', 'orderList');
      Route::get('orderdetails/{id}','OrderDetails');
 Route::post('/CreateReturnSaleOrder','CreateReturnSaleOrder');
 Route::get('/ReturnSaleOrderList','ReturnSaleOrderList');

    });

    Route::controller('DistributorController')->prefix("distributor")->group(function(){
      Route::get('getTsoWiseDistributor', "getTsoWiseDistributor");
    });
    Route::controller('RouteController')->prefix("route")->group(function(){
    Route::get('GetTsoByDistributor/{distributor_id}', 'GetTsoByDistributor');
      Route::get('getTsoDistributorWiseRoute/{distributor_id}', "getTsoDistributorWiseRoute");
 Route::get('getTsoDistributorWiseRoute_shop/{distributor_id}', "getTsoDistributorWiseRoute_shop");
 Route::get('getTsoDistributorWiseRoute_new/{distributor_id}', "getTsoDistributorWiseRoute_new");
      Route::get('getRoutePlan/{distributor_id}', "getRoutePlan");
    });

    Route::controller('DashboardController')->prefix("dashboard")->group(function(){
      Route::get('getDashboardSummary', "getDashboardSummary");
      Route::get('getTsoTarget', "getTsoTarget");
      Route::get('getDetailTsoTarget', "getDetailTsoTarget");

    });


    Route::controller('PaymentController')->prefix("payment")->group(function(){
      Route::post('storePayment', "storePayment");
      Route::get('paymentList', "paymentList");
    });


    Route::controller('addProduct', 'UserController');

    Route::controller('RackController')->group(function(){
        Route::get('rack/search', "search");
        Route::resource('rack', 'RackController');
        Route::get('rack_list', "rack_list");
        Route::post('assign_rack', "assign_rack");
        Route::post('reclaim_rack', "reclaim_rack");

        Route::post('scan_rack', "scan_rack");

    });
    // Route::get('rack/search','RackController@search');
  });
});
