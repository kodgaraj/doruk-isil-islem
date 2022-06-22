<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
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

Route::post("/giris", [ApiController::class, "giris"]);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware([])->group(function () {
    // Route::any('/{controller}/{action}', function ($controller, $action, Closure $next) {
    //     return new (lcfirst($controller) . Controller::class)->{$action}();
    // });
    Route::get('/', function () {
        // Uses first & second middleware...
    });

    // Route::get('/user/profile', function () {
    //     // Uses first & second middleware...
    // });
});
