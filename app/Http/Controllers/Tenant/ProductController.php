<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $products = Product::with('category')->latest()->paginate(10);
        
        return Inertia::render('Tenant/Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Render the create product page (Vue component).
     */
    public function create(): Response
    {
        $categories = Category::all(['id', 'name']);
        
        return Inertia::render('Tenant/Products/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store the product and its dynamic attributes.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Handle image upload
            $images = [];
            if ($request->hasFile('image')) {
                $disk = env('AWS_BUCKET') ? 's3' : 'public';
                $path = $request->file('image')->store('products', $disk);
                $images[] = $path; // We keep it as an array to respect the JSON column design
            }

            // Create the main product
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'sku'         => $validated['sku'],
                'name'        => $validated['name'],
                'slug'        => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'price'       => $validated['price'],
                'sale_price'  => $validated['sale_price'] ?? null,
                'show_price'  => $validated['show_price'],
                'stock'       => $validated['stock'],
                'show_stock'  => $validated['show_stock'],
                'status'      => $validated['status'],
                'images'      => !empty($images) ? $images : null,
            ]);

            // Save dynamic attribute values
            if (!empty($validated['attributes'])) {
                $attributeValues = [];
                foreach ($validated['attributes'] as $attributeId => $value) {
                    if ($value !== null && $value !== '') {
                        $attributeValues[] = [
                            'attribute_id' => $attributeId,
                            'value'        => is_array($value) ? json_encode($value) : (string) $value,
                        ];
                    }
                }
                
                if (count($attributeValues) > 0) {
                    $product->attributeValues()->createMany($attributeValues);
                }
            }

            DB::commit();

            return redirect()->route('tenant.products.index')->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): Response
    {
        $categories = Category::all(['id', 'name']);
        $product->load('attributeValues');
        
        return Inertia::render('Tenant/Products/Edit', [
            'categories' => $categories,
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Handle image upload
            $images = $product->images ?? [];
            if ($request->hasFile('image')) {
                // Delete old image if it exists
                if (!empty($images) && isset($images[0])) {
                    $disk = env('AWS_BUCKET') ? 's3' : 'public';
                    Storage::disk($disk)->delete($images[0]);
                }
                
                $disk = env('AWS_BUCKET') ? 's3' : 'public';
                $path = $request->file('image')->store('products', $disk);
                $images = [$path]; // Replace with new image
            }

            $product->update([
                'category_id' => $validated['category_id'],
                'sku'         => $validated['sku'],
                'name'        => $validated['name'],
                'slug'        => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'price'       => $validated['price'],
                'sale_price'  => $validated['sale_price'] ?? null,
                'show_price'  => $validated['show_price'],
                'stock'       => $validated['stock'],
                'show_stock'  => $validated['show_stock'],
                'status'      => $validated['status'],
                'images'      => !empty($images) ? $images : null,
            ]);

            // Save dynamic attribute values (sync logic)
            if (!empty($validated['attributes'])) {
                // To keep it simple, we delete existing and recreate
                $product->attributeValues()->delete();
                
                $attributeValues = [];
                foreach ($validated['attributes'] as $attributeId => $value) {
                    if ($value !== null && $value !== '') {
                        $attributeValues[] = [
                            'attribute_id' => $attributeId,
                            'value'        => is_array($value) ? json_encode($value) : (string) $value,
                        ];
                    }
                }
                
                if (count($attributeValues) > 0) {
                    $product->attributeValues()->createMany($attributeValues);
                }
            } else {
                $product->attributeValues()->delete();
            }

            DB::commit();

            return redirect()->route('tenant.products.index')->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            if (!empty($product->images) && isset($product->images[0])) {
                $disk = env('AWS_BUCKET') ? 's3' : 'public';
                Storage::disk($disk)->delete($product->images[0]);
            }
            
            $product->delete();
            
            return redirect()->route('tenant.products.index')->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get attributes for a specific category.
     * Returns admin-editable attributes (text, number, boolean) separately
     * from buyer-interactive attributes (select, textarea).
     */
    public function getAttributesByCategory($categoryId): JsonResponse
    {
        $category = Category::with(['attributes' => function ($query) {
            $query->select('attributes.id', 'attributes.name', 'attributes.type', 'attributes.options');
        }])->findOrFail($categoryId);

        // Atributos que el ADMIN gestiona al crear el producto (características fijas)
        $adminAttributes = $category->attributes
            ->filter(fn($attr) => in_array($attr->type, ['text', 'number', 'boolean']))
            ->map(function ($attribute) {
                return [
                    'id'          => $attribute->id,
                    'name'        => $attribute->name,
                    'type'        => $attribute->type,
                    'options'     => $attribute->options,
                    'is_required' => (bool) $attribute->pivot->is_required,
                ];
            })->values();

        // Atributos que el COMPRADOR interactúa en el catálogo (select, textarea)
        $buyerAttributes = $category->attributes
            ->filter(fn($attr) => in_array($attr->type, ['select', 'textarea']))
            ->map(function ($attribute) {
                return [
                    'id'          => $attribute->id,
                    'name'        => $attribute->name,
                    'type'        => $attribute->type,
                    'options'     => $attribute->options,
                    'is_required' => (bool) $attribute->pivot->is_required,
                ];
            })->values();

        return response()->json([
            'attributes'       => $adminAttributes,
            'buyer_attributes' => $buyerAttributes,
        ]);
    }
}
