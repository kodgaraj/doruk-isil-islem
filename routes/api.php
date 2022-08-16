<?php

use App\Http\Controllers\ApiController;
use App\Http\Middleware\JwtVerify;
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

Route::middleware([JwtVerify::class])->group(function () {

    Route::post('/', function (Request $request) {
        return response()->json([
            'status' => true,
            'message' => 'API başarılı',
            'data' => $request->decoded,
        ]);
    });

    Route::any('/{controller}/{action}', function ($controller, $action, Request $request) {
        $controllerClass = "App\\Http\\Controllers\\" . ucfirst($controller) . "Controller";
        return (new $controllerClass)->{$action}($request);
    });
});
