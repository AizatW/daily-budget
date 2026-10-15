<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AuthController;

Route::middleware('auth')->group(function(){
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/expenses/{id}/edit', [ExpenseController::class, 'edit']);

    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/expenses/{id}', [ExpenseController::class , 'update']);

    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
});

Route::middleware('guest')->group(function(){
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');


    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
});




Route::get('/', function () {
    return redirect('/expenses');
});
