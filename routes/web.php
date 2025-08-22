<?php

use App\Http\Controllers\AssetController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [AssetController::class,'index'])->name('assets.index');
    Route::get('/vehicles', [AssetController::class,'vehicles'])->name('assets.vehicles');
    Route::get('/splicers', [AssetController::class,'splicers'])->name('assets.splicers');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/assets/create', [AssetController::class,'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class,'store'])->name('assets.store');
        Route::get('/assets/{asset}/edit', [AssetController::class,'edit'])->name('assets.edit');
        Route::put('/assets/{asset}', [AssetController::class,'update'])->name('assets.update');
        Route::delete('/assets/{asset}', [AssetController::class,'destroy'])->name('assets.destroy');
    });

    Route::get('/assets/export',[AssetController::class,'export'])->name('assets.export');
});
