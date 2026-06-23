<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_leads', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('phone_number')->nullable();
            $table->string('customer_name')->nullable();
            
            // Relación con el producto del catálogo
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->json('buyer_choices')->nullable();
            
            $table->enum('current_status', [
                'nuevo',
                'en_conversacion',
                'esperando_pago',
                'pagado_pendiente',
                'entregado',
                'archivado'
            ])->default('nuevo');
            
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_leads');
    }
};
