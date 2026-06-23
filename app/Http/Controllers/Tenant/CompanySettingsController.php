<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompanySettingsController extends Controller
{
    /**
     * Show the company settings form.
     */
    public function edit(): Response
    {
        return Inertia::render('Tenant/Settings/Company', [
            // The tenant info is already shared via HandleInertiaRequests middleware
            // but we can pass it explicitly or just rely on $page.props.tenant
        ]);
    }

    /**
     * Update the company settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Max 2MB
        ]);

        $tenant = tenant();

        // Update company name
        $tenant->company_name = $validated['company_name'];

        // Handle image upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if it exists
            if ($tenant->company_logo) {
                Storage::delete($tenant->company_logo);
            }
            
            $path = $request->file('company_logo')->storePublicly('logos');
            // Setting a non-custom column directly goes into the JSON 'data' column
            $tenant->company_logo = $path;
        }

        // We use save() to persist changes to the tenants table (central DB)
        $tenant->save();

        return redirect()->route('tenant.settings.company.edit')->with('success', 'Ajustes de empresa guardados exitosamente.');
    }
}
