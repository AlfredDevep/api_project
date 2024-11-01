<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\ApiTokenMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// rutas publicas
Route::prefix('V1')->group(function () {
    // Ruta para Login
    Route::post('/login', [LoginController::class, 'login']);
    
    // ruta para crear un nuevo usuario
    Route::post('/users/new', [UsersController::class, 'store']);
});
//rutas protegidas 
Route::middleware([ApiTokenMiddleware::class])->group(function () {
    // prefix version for the route
    Route::prefix('V1')->group(function () {

        // ruta para mostrar los usuarios
        Route::get('/users', [UsersController::class, 'index']);

        // ruta para mostrar un usuario
        Route::get('/users/{id}', [UsersController::class,'show']);

        // ruta para actualizar un usuario
        Route::put('/users/{id}', [UsersController::class, 'update']);

        // ruta para eliminar un usuario
        Route::delete('/users/{id}', [UsersController::class, 'destroy']);

        // ruta para mostrar todos los productos
        Route::get('/products', [ProductController::class, 'index']);

        // ruta para mostrar un producto
        Route::get('/products/{id}', [ProductController::class,'show']);

        // ruta para crear un nuevo producto
        Route::post('/products/new', [ProductController::class,'store']);

        // ruta para actualizar un producto
        Route::put('/products/{id}', [ProductController::class, 'update']);

        // ruta para eliminar un producto
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // ruta para buscar un producto por su nombre
        Route::get('/products/search/{name}', [ProductController::class,'searchProductByNameLike']);

        // ruta para buscar producto por rango de precios
        Route::get('/products/price_range/{min_price}/{max_price}', [ProductController::class,'searchProductByPriceRange']);

        // ruta para mostrar todos los servicios
        Route::get('/services', [ServiceController::class, 'index']);

        // ruta para crear un nuevo servicio
        Route::post('/services/new', [ServiceController::class,'store']);;
    });
});