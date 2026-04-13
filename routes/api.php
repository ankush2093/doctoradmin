<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BookingEnquiryController;
use App\Http\Controllers\RoleController;    
use App\Http\Controllers\DoctorReceiptController;    

// Public auth routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/admin-users/create', [AdminUserController::class, 'create']);

// Public Website Routes
Route::prefix('website')->group(function () {
    Route::post('/receipt', [DoctorReceiptController::class, 'createReceipt']);
    Route::get('/receipt', [DoctorReceiptController::class, 'getAllReceipts']);
    Route::get('/receipt/{id}', [DoctorReceiptController::class, 'getReceiptById']);
    Route::put('/receipt/{id}', [DoctorReceiptController::class, 'updateReceipt']);
    Route::delete('/receipt/{id}', [DoctorReceiptController::class, 'deleteReceipt']);
});

// Protected auth routes
Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Admin & Editor: Full CRUD access
    Route::middleware('role:admin,editor')->group(function () {

        // Hero Sections
        Route::prefix('hero-sections')->group(function () {
            Route::post('/create', [HeroSectionController::class, 'create']);
            Route::post('/update/{id}', [HeroSectionController::class, 'update']);
            Route::get('/all', [HeroSectionController::class, 'getAll']);
            Route::get('/{id}', [HeroSectionController::class, 'getById']);
            Route::delete('/delete/{id}', [HeroSectionController::class, 'delete']);
        });

        // Booking Enquiry (Admin view/delete only)
        Route::prefix('booking-enquiries')->group(function () {
            Route::get('/all', [BookingEnquiryController::class, 'getAll']);
            Route::get('/{id}', [BookingEnquiryController::class, 'getById']);
            Route::delete('/delete/{id}', [BookingEnquiryController::class, 'delete']);
        });

        // Roles
        Route::prefix('roles')->group(function () {
            Route::post('/create', [RoleController::class, 'create']);
            Route::post('/update/{id}', [RoleController::class, 'update']);
            Route::get('/all', [RoleController::class, 'getAll']);
            Route::get('/{id}', [RoleController::class, 'getById']);
            Route::delete('/delete/{id}', [RoleController::class, 'delete']);
        });

    });
});