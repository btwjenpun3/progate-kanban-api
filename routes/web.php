<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskFileController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [TaskController::class, 'home'])
    ->name('home')
    ->middleware('auth');


// Route::get('/', [HomeController::class, 'welcome']);

// Route::prefix('tasks')
//     ->name('tasks.')
//     ->controller(TaskController::class)
//     ->group(function () {
//         Route::get('/', 'index')->name('index');
//         Route::get('{id}/edit', 'edit')->name('edit');
//         Route::get('create', 'create')->name('create');
//     });

Route::prefix('tasks')
    ->name('tasks.')
    ->middleware('auth')
    ->controller(TaskController::class)
    ->group(function () {
        Route::controller(TaskController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create/{status?}', 'create')->name('create');
            Route::post('/', 'store')->name('store');  // Ditambahkan
            Route::get('{id}/edit', 'edit')->name('edit');
            Route::put('{id}/edit', 'update')->name('update');
            Route::get('{id}/delete', 'delete')->name('delete');
            Route::delete('{id}/destroy', 'destroy')->name('destroy');
            Route::get('progress', 'progress')->name('progress');
            Route::patch('{id}/move', 'move')->name('move');
            Route::get('{id}/cardcomplete', 'cardComplete')->name('cardComplete');
            Route::get('{id}/listcomplete', 'listComplete')->name('listComplete');
        });

        Route::prefix('{task_id}/files')
            ->name('files.')
            ->controller(TaskFileController::class)
            ->group(function () {
                Route::post('store', 'store')->name('store');
                Route::get('{id}/show', 'show')->name('show');
                Route::delete('{id}/destroy', 'destroy')->name('destroy');
            });
    });

Route::name('auth.')
    ->controller(AuthController::class)
    ->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('signup', 'signupForm')->name('signupForm');
            Route::post('signup', 'signup')->name('signup');
            Route::get('login', 'loginForm')->name('loginForm');
            Route::post('login', 'login')->name('login');
        });

        Route::middleware('auth')->group(function () {
            Route::post('logout', 'logout')->name('logout');
        });
    });

Route::prefix('roles')
    ->name('roles.')
    ->middleware('auth')
    ->controller(RoleController::class)
    ->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::put('{id}/update', 'update')->name('update');
        Route::get('{id}/delete', 'delete')->name('delete');
        Route::delete('{id}/destroy', 'destroy')->name('destroy');
    });

Route::prefix('users')
    ->name('users.')
    ->middleware('auth:sanctum')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('{id}/edit-role', 'editRole')->name('editRole');
        Route::put('{id}/update-role', 'updateRole')->name('updateRole');
    });

Route::get('/halo', function () {
    return '<h1>Halo, Ninja!</h1>';
});

Route::resource('users', UserController::class);

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
 
    return ['token' => $token->plainTextToken];
});