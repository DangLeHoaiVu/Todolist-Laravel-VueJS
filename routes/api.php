<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\StatusSettingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::post('/login', [AuthController::class, 'login']);
Route::group(['middleware' => 'web'], function () {
    Route::get('/users', [UserController::class, 'getAllUser']);
    Route::get('/user/{id}', [UserController::class, 'getUserById']);

    Route::get('/status', [StatusController::class, 'getAllStatus']);
    Route::post('/status/createStatus', [StatusController::class, 'createStatus']);
    Route::delete('/status/{id}/deleteStatus', [StatusController::class, 'deleteStatus']);

    Route::get('/status_settings', [StatusSettingController::class, 'getAllStatusSetting']);
    Route::post('/status_setting/createStatusSetting', [StatusSettingController::class, 'createStatusSetting']);

    Route::get('/tasks', [TaskController::class, 'getAllTask']);
    Route::get('/task/{id}', [TaskController::class, 'getTaskDetail']);
    Route::put('/task/{id}/status', [TaskController::class, 'updateTaskStatus']);
    Route::put('/task/{id}/assign', [TaskController::class, 'updateUserAssign']);
    Route::post('/task/createTask', [TaskController::class, 'createTask']);
    Route::put('/task/{id}/updateDetailTask', [TaskController::class, 'updateDetailTask']);

    Route::get('/task_log', [TaskLogController::class, 'getAllTaskLog']);
});
