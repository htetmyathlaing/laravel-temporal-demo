<?php

use App\Http\Controllers\WorkFlowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(WorkFlowController::class)
    ->group(function (){
        Route::get('start-first-workflow', 'startFirstWorkflow');
        Route::get('start-async-workflow', 'startAsyncWorkflow');
        Route::get('start-retry-workflow', 'startRetryWorkflow');
        Route::get('start-signal-workflow', 'startSignalWorkflow');
        Route::get('start-timeout-workflow', 'startTimeoutWorkflow');
    });
