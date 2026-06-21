<?php

use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

// Redirigir la raíz al panel de administración de tenants
Route::get('/', function () {
    return redirect()->route('admin.tenants.index');
});

// Panel central de administración de Tenants
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
});
