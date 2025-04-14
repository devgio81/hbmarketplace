<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;

Route::get('/', [ContractController::class, 'show'])->name('contracts.show');
Route::post('/contract', [ContractController::class, 'update'])->name('contracts.update');
Route::post('/rate-limit', [ContractController::class, 'updateRateLimit'])->name('rate-limit.update');
