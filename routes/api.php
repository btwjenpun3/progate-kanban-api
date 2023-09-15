<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Auth;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// | ============================================================= | //

// | Auth Route | //

Route::post('/auth/user/login', [AuthController::class, 'login']);

Route::post('/auth/user/signup', [AuthController::class, 'signup']);

Route::delete('/auth/user/token/revoke', [AuthController::class, 'revoke'])->middleware('auth:sanctum');

// | ============================================================= | //

// | Home Route | //

Route::get('/home', [HomeController::class, 'index'])->middleware('auth:sanctum');

// | ============================================================= | //

// | Task Route | //

Route::get('/tasks', [TaskController::class, 'index'])->middleware('auth:sanctum');

Route::get('tasks/{id}', [TaskController::class, 'indexById'])->middleware('auth:sanctum');

Route::post('/tasks/store', [TaskController::class, 'store'])->middleware('auth:sanctum');

Route::post('/tasks/{id}/edit', [TaskController::class, 'update'])->middleware('auth:sanctum');

Route::post('/task/{id}/move', [TaskController::class, 'move'])->middleware('auth:sanctum');

Route::delete('/tasks/{id}/destroy', [TaskController::class, 'destroy'])->middleware('auth:sanctum');

// | ============================================================= | //

// | Role Route | //

Route::get('/roles', [RoleController::class, 'index'])->middleware('auth:sanctum');

Route::post('/roles/create', [RoleController::class, 'create'])->middleware('auth:sanctum');

Route::post('/roles/{id}/edit', [RoleController::class, 'edit'])->middleware('auth:sanctum');

Route::delete('/roles/{id}/delete', [RoleController::class, 'destroy'])->middleware('auth:sanctum');

// | ============================================================= | //

// | User Route | //

Route::get('/users', [UserController::class, 'index'])->middleware('auth:sanctum');
