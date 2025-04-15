<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\AssetHistoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route for all users
Route::middleware('auth:sanctum')->get('/assets', [AssetController::class, 'index']);
Route::middleware('auth:sanctum')->get('/histories', [AssetHistoryController::class, 'index']);
Route::middleware('auth:sanctum')->get('/assets/{id}/histories', [AssetHistoryController::class, 'showByAsset']);
Route::middleware('auth:sanctum')->get('/histories/filter', [AssetHistoryController::class, 'filter']);
Route::middleware('auth:sanctum')->get('/maintenance', [MaintenanceController::class, 'index']);
Route::middleware('auth:sanctum')->get('/assets/search', [AssetController::class, 'search']);
Route::middleware('auth:sanctum')->get('/assets/list', [AssetController::class, 'list']);
Route::middleware('auth:sanctum')->get('/stats/assets-by-location', [AssetController::class, 'assetsByLocation']);
Route::middleware('auth:sanctum')->get('/assets/statistics/year', [AssetController::class, 'assetsByYear']);
Route::middleware('auth:sanctum')->get('/stats/maintenance-by-status', [MaintenanceController::class, 'maintenanceByStatus']);
Route::middleware('auth:sanctum')->get('/stats/assets-by-category', [AssetController::class, 'assetsByCategory']);
Route::middleware('auth:sanctum')->get('/dashboard/summary', [DashboardController::class, 'summary']);

//*** Role Admin routes ***
Route::middleware('auth:sanctum', 'role:Admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', fn(Request $request) => $request->users());
    
    // Asset routes
    Route::get('/assets/{id}', [AssetController::class, 'show']);// Detail Aset
    Route::post('/assets', [AssetController::class, 'store']); // Tambah Aset
    Route::put('/assets/{id}', [AssetController::class, 'update']); // Update Aset
    Route::delete('/assets/{id}', [AssetController::class, 'destroy']);// Hapus Aset

    // Maintenance routes
    Route::get('/maintenance/{id}', [MaintenanceController::class, 'show']);
    Route::post('/maintenance', [MaintenanceController::class, 'store']);
    Route::patch('/maintenance/{id}', [MaintenanceController::class, 'update']);
    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy']);
    

    // Manage User routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']); // Update user
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});


//*** Role Teknisi routes ***
Route::middleware('auth:sanctum', 'role:Teknisi')->group(function (){
    Route::post('/logout', [AuthController::class, 'logout']);

    // Maintenance routes
    Route::patch('/maintenance/result/{id}', [MaintenanceController::class, 'updateResult']); // Update Hasil sudah selesai
});


//*** Role Manager routes ***
Route::middleware('auth:sanctum','role:Manager')->group(function (){
    Route::post('/logout', [AuthController::class, 'logout']);

    // Maintenance routes
    Route::put('/maintenance/{id}', [MaintenanceController::class, 'update']);// Update Jadwal


});

