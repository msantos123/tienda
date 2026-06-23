<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\TenantAuthController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
| Estas rutas son accesibles usando la ruta del tenant como prefijo (ej: /vertice).
| El middleware InitializeTenancyByPath identifica el tenant basándose en el
| parámetro {tenant} y cambia el contexto de la base de datos a su schema en PostgreSQL.
|*/

Route::middleware([
    'web',
    InitializeTenancyByPath::class,
])->prefix('{tenant}')->group(function () {

    // Ruta raíz del tenant
    Route::get('/', function () {
        return redirect()->route('tenant.login');
    });

    // Login del tenant
    Route::get('/login', [TenantAuthController::class, 'create'])->name('tenant.login');
    Route::post('/login', [TenantAuthController::class, 'store'])->name('tenant.login.store');

    // Logout
    Route::post('/logout', [TenantAuthController::class, 'destroy'])->name('tenant.logout');

    // ─── Catálogo público (sin autenticación) ───────────────────────────────
    Route::get('/catalogo', [\App\Http\Controllers\Tenant\CatalogController::class, 'index'])->name('tenant.catalog.index');
    Route::get('/catalogo/{slug}', [\App\Http\Controllers\Tenant\CatalogController::class, 'show'])->name('tenant.catalog.show');
    Route::post('/whatsapp-leads/store', [\App\Http\Controllers\Tenant\WhatsappLeadController::class, 'storePublic'])->name('tenant.whatsapp-leads.store-public');
    Route::post('/whatsapp-leads/store-cart', [\App\Http\Controllers\Tenant\WhatsappLeadController::class, 'storeCartPublic'])->name('tenant.whatsapp-leads.store-cart-public');


    // Dashboard del tenant (protegido por auth)
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            // Intentar contar leads, si la tabla no existe (ej. falta migración) devolver 0
            $activeLeadsCount = 0;
            try {
                $activeLeadsCount = \App\Models\WhatsappLead::whereIn('current_status', ['nuevo', 'en_conversacion'])->count();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('No se pudo contar WhatsappLeads en Dashboard: ' . $e->getMessage());
            }

            return inertia('Tenant/Dashboard', [
                'user'          => auth()->user(),
                'tenantId'      => tenant('id'),
                'productsCount' => \App\Models\Product::count(),
                'activeLeadsCount' => $activeLeadsCount,
            ]);
        })->name('tenant.dashboard');

        // Mini-CRM de WhatsApp Leads
        Route::get('/whatsapp-leads', [\App\Http\Controllers\Tenant\WhatsappLeadController::class, 'index'])->name('tenant.whatsapp-leads.index');
        Route::put('/whatsapp-leads/{lead}', [\App\Http\Controllers\Tenant\WhatsappLeadController::class, 'update'])->name('tenant.whatsapp-leads.update');
        Route::post('/whatsapp-leads/{lead}/status', [\App\Http\Controllers\Tenant\WhatsappLeadController::class, 'updateStatus'])->name('tenant.whatsapp-leads.update-status');
        Route::delete('/whatsapp-leads/{lead}', [\App\Http\Controllers\Tenant\WhatsappLeadController::class, 'destroy'])->name('tenant.whatsapp-leads.destroy');

        // Productos
        Route::get('/products', [\App\Http\Controllers\Tenant\ProductController::class, 'index'])->name('tenant.products.index');
        Route::get('/products/create', [\App\Http\Controllers\Tenant\ProductController::class, 'create'])->name('tenant.products.create');
        Route::post('/products', [\App\Http\Controllers\Tenant\ProductController::class, 'store'])->name('tenant.products.store');
        Route::get('/products/{product}/edit', [\App\Http\Controllers\Tenant\ProductController::class, 'edit'])->name('tenant.products.edit');
        Route::put('/products/{product}', [\App\Http\Controllers\Tenant\ProductController::class, 'update'])->name('tenant.products.update');
        Route::delete('/products/{product}', [\App\Http\Controllers\Tenant\ProductController::class, 'destroy'])->name('tenant.products.destroy');

        // Configuraciones
        Route::get('/settings/company', [\App\Http\Controllers\Tenant\CompanySettingsController::class, 'edit'])->name('tenant.settings.company.edit');
        Route::post('/settings/company', [\App\Http\Controllers\Tenant\CompanySettingsController::class, 'update'])->name('tenant.settings.company.update');
        
        // API Interna para Vue (Atributos dinámicos por categoría)
        Route::get('/api/categories/{category}/attributes', [\App\Http\Controllers\Tenant\ProductController::class, 'getAttributesByCategory'])->name('tenant.api.categories.attributes');

        // Configuraciones
        Route::get('/settings', function () {
            return inertia('Tenant/Settings/Index');
        })->name('tenant.settings.index');

        Route::get('/settings/profile', function () {
            return inertia('Tenant/Settings/Profile', [
                'user' => auth()->user()
            ]);
        })->name('tenant.settings.profile');

        Route::put('/settings/profile', function (\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'name'  => 'required|string|max:255',
                'phone' => 'nullable|string|max:30',
            ]);
            auth()->user()->update($validated);
            return redirect()->route('tenant.settings.profile')->with('success', 'Perfil actualizado correctamente.');
        })->name('tenant.settings.profile.update');


        // Categorías
        Route::get('/settings/categories', [\App\Http\Controllers\Tenant\CategoryController::class, 'index'])->name('tenant.settings.categories.index');
        Route::get('/settings/categories/create', [\App\Http\Controllers\Tenant\CategoryController::class, 'create'])->name('tenant.settings.categories.create');
        Route::post('/settings/categories', [\App\Http\Controllers\Tenant\CategoryController::class, 'store'])->name('tenant.settings.categories.store');
        Route::get('/settings/categories/{category}/edit', [\App\Http\Controllers\Tenant\CategoryController::class, 'edit'])->name('tenant.settings.categories.edit');
        Route::put('/settings/categories/{category}', [\App\Http\Controllers\Tenant\CategoryController::class, 'update'])->name('tenant.settings.categories.update');
        Route::delete('/settings/categories/{category}', [\App\Http\Controllers\Tenant\CategoryController::class, 'destroy'])->name('tenant.settings.categories.destroy');

        // Atributos
        Route::get('/settings/attributes', [\App\Http\Controllers\Tenant\AttributeController::class, 'index'])->name('tenant.settings.attributes.index');
        Route::get('/settings/attributes/create', [\App\Http\Controllers\Tenant\AttributeController::class, 'create'])->name('tenant.settings.attributes.create');
        Route::post('/settings/attributes', [\App\Http\Controllers\Tenant\AttributeController::class, 'store'])->name('tenant.settings.attributes.store');
        Route::get('/settings/attributes/{attribute}/edit', [\App\Http\Controllers\Tenant\AttributeController::class, 'edit'])->name('tenant.settings.attributes.edit');
        Route::put('/settings/attributes/{attribute}', [\App\Http\Controllers\Tenant\AttributeController::class, 'update'])->name('tenant.settings.attributes.update');
        Route::delete('/settings/attributes/{attribute}', [\App\Http\Controllers\Tenant\AttributeController::class, 'destroy'])->name('tenant.settings.attributes.destroy');
    });
});
