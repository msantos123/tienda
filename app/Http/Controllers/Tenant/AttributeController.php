<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        return inertia('Tenant/Settings/Attributes', [
            'attributes' => Attribute::with('categories')->orderBy('id', 'desc')->get()
        ]);
    }

    public function create()
    {
        return inertia('Tenant/Settings/Attributes/Create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,boolean,textarea',
            'options' => 'nullable|array',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        $attribute = Attribute::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'options' => $validated['options'] ?? [],
        ]);

        if (!empty($validated['category_ids'])) {
            $attribute->categories()->attach($validated['category_ids']);
        }

        return redirect()->route('tenant.settings.attributes.index')->with('success', 'Atributo creado exitosamente.');
    }

    public function edit(Attribute $attribute)
    {
        $attribute->load('categories');
        return inertia('Tenant/Settings/Attributes/Edit', [
            'attribute' => $attribute,
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,boolean,textarea',
            'options' => 'nullable|array',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        $attribute->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'options' => $validated['options'] ?? [],
        ]);

        $attribute->categories()->sync($validated['category_ids'] ?? []);

        return redirect()->route('tenant.settings.attributes.index')->with('success', 'Atributo actualizado exitosamente.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('tenant.settings.attributes.index')->with('success', 'Atributo eliminado exitosamente.');
    }
}
