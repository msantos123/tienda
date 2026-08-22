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
                // Usamos el disco por defecto (S3 en Laravel Cloud, public en local)
                $path = $request->file('image')->storePublicly('products');
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
     * Store multiple product variants at once (bulk creation).
     * All variants share: category, price, sale_price, description, stock, status, attributes.
     * Each variant has its own: name, sku, slug, image.
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            // Campos compartidos
            'category_id'        => ['required', 'exists:categories,id'],
            'description'        => ['nullable', 'string'],
            'price'              => ['required', 'numeric', 'min:0'],
            'sale_price'         => ['nullable', 'numeric', 'min:0'],
            'show_price'         => ['required', 'boolean'],
            'stock'              => ['required', 'integer', 'min:0'],
            'show_stock'         => ['required', 'boolean'],
            'status'             => ['required', 'boolean'],
            'attributes'         => ['nullable', 'array'],
            // Array de variantes
            'variants'           => ['required', 'array', 'min:1'],
            'variants.*.name'    => ['required', 'string', 'max:255'],
            'variants.*.sku'     => ['required', 'string', 'max:255', 'distinct', 'unique:products,sku'],
            'variants.*.slug'    => ['required', 'string', 'max:255', 'distinct', 'unique:products,slug'],
            'variants.*.image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'variants.*.name.required'   => 'Cada variante debe tener un nombre.',
            'variants.*.sku.required'    => 'Cada variante debe tener un SKU.',
            'variants.*.sku.unique'      => 'El SKU de la variante ya existe en el sistema.',
            'variants.*.sku.distinct'    => 'Los SKUs de las variantes no pueden repetirse.',
            'variants.*.slug.unique'     => 'El slug de la variante ya existe en el sistema.',
            'variants.*.slug.distinct'   => 'Los slugs de las variantes no pueden repetirse.',
            'variants.*.image.image'     => 'El archivo debe ser una imagen válida.',
            'variants.*.image.max'       => 'La imagen no puede pesar más de 2MB.',
        ]);

        $sharedData = [
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price'       => $request->price,
            'sale_price'  => $request->sale_price,
            'show_price'  => filter_var($request->show_price, FILTER_VALIDATE_BOOLEAN),
            'stock'       => $request->stock,
            'show_stock'  => filter_var($request->show_stock, FILTER_VALIDATE_BOOLEAN),
            'status'      => filter_var($request->status, FILTER_VALIDATE_BOOLEAN),
        ];

        $attributes = $request->input('attributes', []);
        $variants   = $request->input('variants', []);

        DB::beginTransaction();
        try {
            $created = 0;
            foreach ($variants as $index => $variantData) {
                // Subir imagen si viene
                $images = [];
                if ($request->hasFile("variants.{$index}.image")) {
                    $path     = $request->file("variants.{$index}.image")->storePublicly('products');
                    $images[] = $path;
                }

                $product = Product::create(array_merge($sharedData, [
                    'name'   => $variantData['name'],
                    'sku'    => $variantData['sku'],
                    'slug'   => $variantData['slug'],
                    'images' => !empty($images) ? $images : null,
                ]));

                // Atributos compartidos
                if (!empty($attributes)) {
                    $attrValues = [];
                    foreach ($attributes as $attributeId => $value) {
                        if ($value !== null && $value !== '') {
                            $attrValues[] = [
                                'attribute_id' => $attributeId,
                                'value'        => is_array($value) ? json_encode($value) : (string) $value,
                            ];
                        }
                    }
                    if (count($attrValues) > 0) {
                        $product->attributeValues()->createMany($attrValues);
                    }
                }

                $created++;
            }

            DB::commit();

            return redirect()
                ->route('tenant.products.index')
                ->with('success', "{$created} producto(s) creado(s) exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear productos: ' . $e->getMessage());
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
                    Storage::delete($images[0]);
                }
                
                $path = $request->file('image')->storePublicly('products');
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
                Storage::delete($product->images[0]);
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
