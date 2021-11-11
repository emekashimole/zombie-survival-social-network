<?php

use App\Http\Controllers\SurvivorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

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

Route::prefix('v1')->group(function () {

    Route::apiResource('survivors', SurvivorController::class);

    Route::fallback(function () {
        return response()->json([
            "appName" => config('app.name'),
            "version" => "v1",
            "status" => false,
            "message" => "Route Not Found"
        ], Response::HTTP_NOT_FOUND);
    });
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
