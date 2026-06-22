<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('status', true);

        // Búsqueda por nombre o SKU
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('sku', 'ilike', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtro por precio mínimo
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Filtro por precio máximo
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtro solo en stock
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all(['id', 'name', 'slug']);

        // Rango de precios global para el slider
        $priceRange = [
            'min' => (float) Product::where('status', true)->min('price') ?? 0,
            'max' => (float) Product::where('status', true)->max('price') ?? 9999,
        ];

        // Obtener número de WhatsApp del primer usuario del tenant (administrador)
        $adminPhone = User::first()?->phone;

        return inertia('Tenant/Catalog/Index', [
            'products'   => $products,
            'categories' => $categories,
            'priceRange' => $priceRange,
            'filters'    => $request->only(['q', 'category', 'min_price', 'max_price', 'in_stock']),
            'adminPhone' => $adminPhone,
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::with([
            'category.attributes',
            'attributeValues.attribute',
        ])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $adminPhone = User::first()?->phone;

        // Atributos interactivos para el comprador (select, textarea) de la categoría del producto
        $buyerAttributes = collect();
        if ($product->category) {
            $buyerAttributes = $product->category->attributes
                ->filter(fn($attr) => in_array($attr->type, ['select', 'textarea']))
                ->map(fn($attr) => [
                    'id'          => $attr->id,
                    'name'        => $attr->name,
                    'type'        => $attr->type,
                    'options'     => $attr->options,
                    'is_required' => (bool) $attr->pivot->is_required,
                ])->values();
        }

        return inertia('Tenant/Catalog/Show', [
            'product'         => $product,
            'adminPhone'      => $adminPhone,
            'buyerAttributes' => $buyerAttributes,
        ]);
    }
}
