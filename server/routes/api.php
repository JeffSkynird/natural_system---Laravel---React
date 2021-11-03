<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

Route::group(['prefix' => 'v1'], function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::post('login', 'App\Http\Controllers\v1\Seguridad\AuthController@login');
        Route::post('logout', 'App\Http\Controllers\v1\Seguridad\AuthController@logout')->middleware('auth:api');
    });

    Route::middleware('auth:api')->group(function () {

        Route::post('autorize_order/{id}', 'App\Http\Controllers\v1\Inventario\OrderController@autorize');
        Route::put('orders/{id}', 'App\Http\Controllers\v1\Inventario\OrderController@update');

        Route::put('user', 'App\Http\Controllers\v1\Seguridad\UsuarioController@updateAuth');
        Route::get('user', 'App\Http\Controllers\v1\Seguridad\UsuarioController@showAuth');
        Route::post('users', 'App\Http\Controllers\v1\Seguridad\UsuarioController@create');
        Route::put('users/{id}', 'App\Http\Controllers\v1\Seguridad\UsuarioController@update');
        Route::get('users', 'App\Http\Controllers\v1\Seguridad\UsuarioController@index');
        Route::get('users/{id}', 'App\Http\Controllers\v1\Seguridad\UsuarioController@show');
        Route::delete('users/{id}', 'App\Http\Controllers\v1\Seguridad\UsuarioController@delete');

        Route::post('suppliers', 'App\Http\Controllers\v1\Inventario\SupplierController@create');
        Route::put('suppliers/{id}', 'App\Http\Controllers\v1\Inventario\SupplierController@update');
        Route::get('suppliers/{id}', 'App\Http\Controllers\v1\Inventario\SupplierController@show');
        Route::get('suppliers', 'App\Http\Controllers\v1\Inventario\SupplierController@index');
        Route::delete('suppliers/{id}', 'App\Http\Controllers\v1\Inventario\SupplierController@delete');
        Route::post('suppliers/upload_image/{id}', 'App\Http\Controllers\v1\Inventario\SupplierController@subirFoto');

        Route::post('adjustments', 'App\Http\Controllers\v1\Inventario\AdjustmentController@create');
        Route::put('adjustments/{id}', 'App\Http\Controllers\v1\Inventario\AdjustmentController@update');
        Route::get('adjustments/{id}', 'App\Http\Controllers\v1\Inventario\AdjustmentController@show');
        Route::get('adjustments', 'App\Http\Controllers\v1\Inventario\AdjustmentController@index');
        Route::delete('adjustments/{id}', 'App\Http\Controllers\v1\Inventario\AdjustmentController@delete');

        Route::post('products', 'App\Http\Controllers\v1\Inventario\ProductController@create');
        Route::post('products/upload_image/{id}', 'App\Http\Controllers\v1\Inventario\ProductController@subirFoto');
        Route::put('products/{id}', 'App\Http\Controllers\v1\Inventario\ProductController@update');
        Route::get('products/{id}', 'App\Http\Controllers\v1\Inventario\ProductController@show');
        Route::get('products', 'App\Http\Controllers\v1\Inventario\ProductController@index');
        Route::delete('products/{id}', 'App\Http\Controllers\v1\Inventario\ProductController@delete');
        Route::get('inventories', 'App\Http\Controllers\v1\Inventario\InventoryController@index');

        Route::post('clients', 'App\Http\Controllers\v1\Ventas\ClientController@create');
        Route::put('clients/{id}', 'App\Http\Controllers\v1\Ventas\ClientController@update');
        Route::get('clients/{id}', 'App\Http\Controllers\v1\Ventas\ClientController@show');
        Route::get('clients', 'App\Http\Controllers\v1\Ventas\ClientController@index');
        Route::delete('clients/{id}', 'App\Http\Controllers\v1\Ventas\ClientController@delete');

        Route::get('orders/{id}', 'App\Http\Controllers\v1\Inventario\OrderController@show');
        Route::get('orders', 'App\Http\Controllers\v1\Inventario\OrderController@index');
        Route::delete('orders/{id}', 'App\Http\Controllers\v1\Inventario\OrderController@delete');
        Route::post('change_order_status', 'App\Http\Controllers\v1\Inventario\OrderController@storeInventory');
        Route::get('order_inventory/{id}', 'App\Http\Controllers\v1\Inventario\OrderController@viewOrderInventory');
        Route::post('order/warehouse/{id}', 'App\Http\Controllers\v1\Inventario\OrderController@distribuirPedido');
        Route::get('orders_status', 'App\Http\Controllers\v1\Inventario\OrderController@viewStatusOrder');
        Route::get('order_detail/{id}', 'App\Http\Controllers\v1\Inventario\OrderController@obtenerDetallePedido');
        Route::post('orders', 'App\Http\Controllers\v1\Inventario\OrderController@create');

        Route::post('invoices', 'App\Http\Controllers\v1\Ventas\InvoiceController@create');
        Route::put('invoices/{id}', 'App\Http\Controllers\v1\Ventas\InvoiceController@update');
        Route::get('invoices/{id}', 'App\Http\Controllers\v1\Ventas\InvoiceController@show');
        Route::get('invoices', 'App\Http\Controllers\v1\Ventas\InvoiceController@index');
        Route::delete('invoices/{id}', 'App\Http\Controllers\v1\Ventas\InvoiceController@delete');

        Route::get('kpis', 'App\Http\Controllers\v1\Ventas\ClientController@kpis');
        Route::get('kardex', 'App\Http\Controllers\v1\Ventas\KardexController@index');
        Route::get('reporte', 'App\Http\Controllers\v1\Reporte\ReporteController@reporte');

        Route::post('unities', 'App\Http\Controllers\v1\Inventario\UnityController@create');
        Route::put('unities/{id}', 'App\Http\Controllers\v1\Inventario\UnityController@update');
        Route::get('unities/{id}', 'App\Http\Controllers\v1\Inventario\UnityController@show');
        Route::get('unities', 'App\Http\Controllers\v1\Inventario\UnityController@index');
        Route::delete('unities/{id}', 'App\Http\Controllers\v1\Inventario\UnityController@delete');

        Route::post('categories', 'App\Http\Controllers\v1\Inventario\CategoryController@create');
        Route::put('categories/{id}', 'App\Http\Controllers\v1\Inventario\CategoryController@update');
        Route::get('categories/{id}', 'App\Http\Controllers\v1\Inventario\CategoryController@show');
        Route::get('categories', 'App\Http\Controllers\v1\Inventario\CategoryController@index');
        Route::delete('categories/{id}', 'App\Http\Controllers\v1\Inventario\CategoryController@delete');

        Route::post('reasons', 'App\Http\Controllers\v1\Inventario\ReasonController@create');
        Route::put('reasons/{id}', 'App\Http\Controllers\v1\Inventario\ReasonController@update');
        Route::get('reasons/{id}', 'App\Http\Controllers\v1\Inventario\ReasonController@show');
        Route::get('reasons', 'App\Http\Controllers\v1\Inventario\ReasonController@index');
        Route::delete('reasons/{id}', 'App\Http\Controllers\v1\Inventario\ReasonController@delete');
    });
});
