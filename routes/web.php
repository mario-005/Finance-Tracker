<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication routes (login, register, password reset)
require __DIR__.'/auth.php';

// Finance app routes (protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/ai/chat', [App\Http\Controllers\AIChatController::class, 'ask'])->name('ai.chat');
    Route::resource('transactions', App\Http\Controllers\TransactionController::class);
    Route::resource('budgets', App\Http\Controllers\BudgetController::class);
    Route::resource('reports', App\Http\Controllers\ReportController::class);
});

