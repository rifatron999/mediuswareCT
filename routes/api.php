<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\TransactionController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/



Route::post('users',[RegisterController::class,'register']);
Route::post('login',[RegisterController::class,'login']);


Route::group(['middleware' => 'auth:sanctum'],function(){
    //Route::resource('tasks',TaskController::class)->only(['update'])->middleware('restrictPosition:team_mate');
   /* Route::get('tasks',[TaskController::class,'index'])->middleware('restrictPosition:team_mate,manager');
    Route::put('tasks/{id}',[TaskController::class,'update'])->middleware('restrictPosition:team_mate');

    Route::post('tasks',[TaskController::class,'store'])->middleware('restrictPosition:manager');
    route::post('team_mate',[RegisterController::class,'create_team_mate'])->middleware('restrictPosition:manager');*/

    Route::get('show',[TransactionController::class,'index']);

    Route::get('deposit',[TransactionController::class,'depositGet']);
    Route::post('deposit',[TransactionController::class,'depositStore']);

    Route::get('withdrawal',[TransactionController::class,'withdrawalGet']);
    Route::post('withdrawal',[TransactionController::class,'withdrawalStore']);

    Route::get('logout', [RegisterController::class,'logout']);
});