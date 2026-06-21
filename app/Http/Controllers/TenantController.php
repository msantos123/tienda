<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('domains')->get();
        return Inertia::render('Admin/Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Tenants/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id'                  => ['required', 'string', 'alpha_num', 'unique:tenants,id', 'max:255'],
            'company_name'        => ['required', 'string', 'max:255'],
            'admin_name'          => ['required', 'string', 'max:255'],
            'admin_email'         => ['required', 'string', 'email', 'max:255'],
            'admin_password'      => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Crear el tenant en la BD central
        $tenant = Tenant::create([
            'id'           => $request->id,
            'company_name' => $request->company_name,
        ]);

        // 2. Crear el dominio dinámicamente según el host central actual (ej: id.localhost o id.tu-app.laravel.run)
        $tenant->domains()->create([
            'domain' => $request->id . '.' . $request->getHost(),
        ]);

        // 3. Inicializar contexto del tenant para crear el usuario admin
        tenancy()->initialize($tenant);

        User::create([
            'name'     => $request->admin_name,
            'email'    => $request->admin_email,
            'password' => Hash::make($request->admin_password),
        ]);

        // 4. Volver al contexto central
        tenancy()->end();

        return redirect()->route('admin.tenants.index')
            ->with('success', "Tenant '{$request->company_name}' aprovisionado correctamente. Schema: tenant_{$request->id}");
    }
}
