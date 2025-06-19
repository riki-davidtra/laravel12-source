<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login_process'])->name('login.process');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'register_process'])->name('register.process');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::resource('profiles', ProfileController::class);
    Route::resource('users', UserController::class);
    Route::post('/users/bulk-delete', [UserController::class, 'bulk_delete'])->name('users.bulk_delete');
    Route::resource('roles', RoleController::class);
    Route::post('/roles/bulk-delete', [RoleController::class, 'bulk_delete'])->name('roles.bulk_delete');
    Route::resource('permissions', PermissionController::class);
    Route::post('/permissions/bulk-delete', [PermissionController::class, 'bulk_delete'])->name('permissions.bulk_delete');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/update-all', [SettingController::class, 'update_all'])->name('settings.update_all');
});
