<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [FinanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [FinanceController::class, 'analytics'])->name('analytics');
    
    // Transactions
    Route::get('/transactions', [FinanceController::class, 'transactions'])->name('transactions.index');
    Route::post('/transactions', [FinanceController::class, 'storeTransaction'])->name('transactions.store');
    Route::delete('/transactions/{transaction}', [FinanceController::class, 'destroyTransaction'])->name('transactions.destroy');
    
    // Accounts
    Route::get('/accounts', [FinanceController::class, 'accounts'])->name('accounts.index');
    Route::post('/accounts', [FinanceController::class, 'storeAccount'])->name('accounts.store');
    
    // Budgets
    Route::get('/budgets', [FinanceController::class, 'budgets'])->name('budgets.index');
    Route::post('/budgets', [FinanceController::class, 'storeBudget'])->name('budgets.store');
    Route::delete('/budgets/{budget}', [FinanceController::class, 'destroyBudget'])->name('budgets.destroy');
    Route::post('/budgets/monthly', [FinanceController::class, 'storeMonthlyBudget'])->name('budgets.monthly.store');
    
    // Saving Goals
    Route::get('/goals', [FinanceController::class, 'goals'])->name('goals.index');
    Route::post('/goals', [FinanceController::class, 'storeGoal'])->name('goals.store');
    Route::patch('/saving-goals/{goal}', [FinanceController::class, 'updateGoal'])->name('saving-goals.update');
    Route::delete('/saving-goals/{goal}', [FinanceController::class, 'destroyGoal'])->name('goals.destroy');
    
    // Categories
    Route::get('/categories', [FinanceController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [FinanceController::class, 'storeCategory'])->name('categories.store');
    Route::patch('/categories/{category}', [FinanceController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [FinanceController::class, 'destroyCategory'])->name('categories.destroy');
});
