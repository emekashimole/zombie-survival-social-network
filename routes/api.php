<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
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

/*
|--------------------------------------------------------------------------
| Zombie Survival Social Network (ZSSN) API Routes
|--------------------------------------------------------------------------
|
| Detailed in this documentation are the routes used for interacting with the
| ZSSN application.
| 
| Description                   | URI
|--------------------------------------------------------------------------
| To list survivors             | GET /api/v1/survivors
| To create a survivor          | POST /api/v1/survivors
| To get a single survivor      | GET /api/v1/survivors/{id}
| To update a survivor          | PUT /api/v1/survivors/{id}
| To delete a survivor          | DELETE /api/v1/survivors/{id}
| To flag an infected survivor  | PUT /api/v1/survivors/{id}/flag
| To get items of a survivor    | GET /api/v1/survivors/{id}/items
| To list items                 | GET /api/v1/items
| To create an item             | POST /api/v1/items
| To get a single item          | GET /api/v1/items/{id}
| To update an item             | PUT /api/v1/items/{id}
| To delete an item             | DELETE /api/v1/items/{id}
| To trade items                | POST /api/v1/items/trade
| To get generated report       | GET /api/v1/report
| 
*/

Route::prefix('v1')->group(function () {

    Route::apiResource('survivors', SurvivorController::class);
    Route::put('survivors/{id}/flag', [SurvivorController::class, 'flagSurvivor']);
    Route::get('survivors/{id}/items', [SurvivorController::class, 'getSurvivorItems']);

    Route::apiResource('items', ItemController::class);
    Route::post('items/trade', [ItemController::class, 'tradeItems']);

    Route::get('report', [ReportController::class, 'generateReport']);

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
