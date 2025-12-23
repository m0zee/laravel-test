<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

// Google Wallet Routes
Route::get('/wallet', [HomeController::class, 'index'])->name('wallet.index');
Route::post('/wallet/add', [HomeController::class, 'addToWallet'])->name('wallet.add');
Route::post('/wallet/generate', [HomeController::class, 'generatePass'])->name('wallet.generate');
Route::post('/wallet/create-class', [HomeController::class, 'createClass'])->name('wallet.create-class');

