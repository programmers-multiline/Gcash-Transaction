<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


Route::view('/', 'login');
Route::view('/treasury', 'treasury_notif');

Route::post('/', [UserController::class, 'auth_login'])->name('login');
Route::get('/logout', [UserController::class, 'auth_logout'])->name('logout');


Route::post('/gform-webhook', [GoogleSheetController::class, 'webhook']);

Route::middleware(['auth'])->group(function () {   

    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('/transactions', [TransactionController::class, 'fetch_transactions'])->name('fetch_transactions');
    Route::post('/transactions/edit', [TransactionController::class, 'edit_transactions'])->name('edit_transactions');
    Route::post('/transactions/delete', [TransactionController::class, 'delete_transaction'])->name('delete_transaction');
    Route::post('/transactions/approval', [TransactionController::class, 'approve_transaction'])->name('approve_transaction');
    Route::post('/transactions/decline', [TransactionController::class, 'decline_transaction'])->name('decline_transaction');
    Route::get('/transactions/approved', [TransactionController::class, 'fetch_transactions_approved'])->name('fetch_transactions_approved');
    Route::get('/transactions/declined', [TransactionController::class, 'fetch_transactions_declined'])->name('fetch_transactions_declined');
    Route::get('/transactions/approver_modal', [TransactionController::class, 'fetch_transactions_approver_modal'])->name('fetch_transactions_approver_modal');
    Route::get('/transactions/approver', [TransactionController::class, 'fetch_transactions_approver'])->name('fetch_transactions_approver');
    Route::post('/transactions/revert_status', [TransactionController::class, 'revert_status'])->name('revert_status');
    Route::post('/upload_transaction_acc', [TransactionController::class, 'upload_transaction_acc'])->name('upload_transaction_acc');
    Route::post('/upload_transaction_approver', [TransactionController::class, 'upload_transaction_approver'])->name('upload_transaction_approver');
    
    Route::get('/transactions/lists', [TransactionController::class, 'fetch_transaction_modal'])->name('fetch_transaction_modal');
    
    Route::post('upload_transaction', [FileUploadController::class, 'upload_transaction'])->name('upload_transaction');

    Route::get('/sheet-data', [GoogleSheetController::class, 'getSheetData']);


    Route::view('/pages/transactions', 'pages.transactions');
    Route::view('/pages/transactions_acc', 'pages.transactions_acc');
    Route::view('/pages/transaction_logs', 'pages.transaction_logs');
    Route::view('/pages/transaction_treasury', 'pages.transaction_treasury');
    Route::view('/pages/transaction_approver', 'pages.transaction_approver');

    // Route::post('/gform-webhook', [GoogleSheetController::class, 'webhook']);
});






