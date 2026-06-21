<?php
use App\Models\Tenant;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;

$tenant = Tenant::find('tenant2');
tenancy()->initialize($tenant);

$category = Category::firstOrCreate([
    'slug' => Str::slug('Ropa Deportiva')
], [
    'name' => 'Ropa Deportiva'
]);

$attrTalla = Attribute::firstOrCreate([
    'name' => 'Talla'
], [
    'type' => 'select',
    'options' => ['S', 'M', 'L', 'XL']
]);

$attrColor = Attribute::firstOrCreate([
    'name' => 'Color'
], [
    'type' => 'text',
    'options' => null
]);

$attrImpermeable = Attribute::firstOrCreate([
    'name' => 'Es Impermeable'
], [
    'type' => 'boolean',
    'options' => null
]);

// Asignar atributos a la categoría
$category->attributes()->sync([
    $attrTalla->id => ['is_required' => true],
    $attrColor->id => ['is_required' => true],
    $attrImpermeable->id => ['is_required' => false],
]);

tenancy()->end();
echo "Data seeded for tenant1 successfully.\n";
