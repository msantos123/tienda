<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TenantAuthController extends Controller
{
    /**
     * Muestra la vista de login del tenant (Vue via Inertia).
     */
    public function create(): Response
    {
        if (Auth::check()) {
            return Inertia::render('Tenant/Dashboard', [
                'user'     => Auth::user(),
                'tenantId' => tenant('id'),
            ]);
        }

        return Inertia::render('Tenant/Login', [
            'status'   => session('status'),
            'tenantId' => tenant('id'),
        ]);
    }

    /**
     * Procesa el inicio de sesión del tenant.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->route('tenant.dashboard');
    }

    /**
     * Cierra la sesión del usuario del tenant.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login');
    }
}
