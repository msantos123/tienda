<?php

namespace App\Http\Requests\Tenant;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'sku'         => ['required', 'string', 'unique:products,sku', 'max:255'],
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'unique:products,slug', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'sale_price'  => ['nullable', 'numeric', 'min:0'],
            'show_price'  => ['required', 'boolean'],
            'stock'       => ['required', 'integer', 'min:0'],
            'show_stock'  => ['required', 'boolean'],
            'status'      => ['required', 'boolean'],
            'attributes'  => ['nullable', 'array'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Max 2MB
        ];

        // Dynamic validation: if category is selected, enforce required attributes
        if ($this->has('category_id') && $this->category_id) {
            $category = Category::find($this->category_id);
            if ($category) {
                // Get required attributes for this category
                $requiredAttributes = $category->attributes()->wherePivot('is_required', true)->get();
                
                foreach ($requiredAttributes as $attribute) {
                    // For required attributes, we ensure they exist in the attributes array
                    // For example: attributes.5
                    $rules["attributes.{$attribute->id}"] = ['required'];
                }
            }
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'attributes.*.required' => 'Este atributo es obligatorio para la categoría seleccionada.',
            'image.max' => 'La imagen no puede pesar más de 2MB.',
            'image.image' => 'El archivo debe ser una imagen válida (jpg, jpeg, png, webp).',
        ];
    }
}
