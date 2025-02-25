<?php

use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::group(['middleware' => 'auth:sanctum'], function () {

    //Create tokens
    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name);

        return ['token' => $token->plainTextToken];
    });

    // Get user 
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
 

    // Register routes
    Route::resource('tasks', TaskController::class);

});




