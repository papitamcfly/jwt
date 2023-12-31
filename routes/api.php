<?php

use App\Http\Controllers\AdafruitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware'=> 'api','prefix'=> 'auth'], function ($router) {
    Route::post ('/register', [AuthController::class ,'register']);
    Route::post ('/login', [AuthController::class ,"login"]);
    Route::get ('/profile', [AuthController::class ,'profile']);
    Route::get ('/logout', [AuthController::class ,'logout']);
    Route::put ('/update', [AuthController::class ,'update']);
    Route::get ('/activate/{token}', [AuthController::class ,'activate'])->name('activate');
    Route::post ('/regcuarto', [AuthController::class ,'regcuarto']);
    Route::post ('/refresh', [AuthController::class ,'refresh']);
    Route::get ('/recibir-datos', [AuthController::class ,'obtenerDatos']);
    Route::get ('/cuartoesp/{idcuarto}',[AuthController::class,'cuartoesp']);
    Route::delete ('/borrarcuarto/{idcuarto}',[AuthController::class,'borrarcuarto']);
    Route::put ('/editarcuarto/{idcuarto}',[AuthController::class,'editarcuarto']);
});

Route::prefix("Adafruit")->group(function(){
    Route::get('/recibirdatos', [AdafruitController::class, 'getdatos']);
    Route::get('/alarma', [AdafruitController::class, 'ApagarAlarma']);
    Route::get('/leds', [AdafruitController::class, 'LuzLed']);
    Route::get('/puerta', [AdafruitController::class, 'Abrirpuerta']);
    });

