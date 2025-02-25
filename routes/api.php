<?php

use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Api versioninig 
Route::prefix('v1')->group(base_path('routes/api/api_v1.php'));


