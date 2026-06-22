<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sku', 'name', 'slug', 'description', 
        'price', 'sale_price', 'show_price', 'stock', 'show_stock', 'status', 'images'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'show_price' => 'boolean',
        'show_stock' => 'boolean',
        'status' => 'boolean',
        'images' => 'array',
    ];

    protected $appends = ['image_urls'];

    public function getImageUrlsAttribute()
    {
        if (empty($this->images)) {
            return [];
        }
        $disk = !empty(config('filesystems.disks.s3.bucket')) ? 's3' : 'public';
        return array_map(function ($path) use ($disk) {
            return \Illuminate\Support\Facades\Storage::disk($disk)->url($path);
        }, $this->images);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
