<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappLead extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_leads';

    protected $fillable = [
        'reference',
        'phone_number',
        'customer_name',
        'product_id',
        'quantity',
        'buyer_choices',
        'current_status',
        'total_amount'
    ];

    protected $casts = [
        'buyer_choices' => 'array',
        'total_amount' => 'decimal:2',
        'quantity' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($lead) {
            if (empty($lead->reference)) {
                $lead->reference = self::generateUniqueReference();
            }
        });
    }

    public static function generateUniqueReference()
    {
        do {
            $reference = 'REF-' . rand(1000, 9999);
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
