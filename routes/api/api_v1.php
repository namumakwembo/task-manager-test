<?php
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {

    //Get authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    //Create tokens
    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name);

        return ['token' => $token->plainTextToken];
    });


    // Register routes
    Route::resource('tasks', TaskController::class);

});


