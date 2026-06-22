<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL: Añadir 'textarea' al enum de tipos de atributos
        DB::statement("ALTER TABLE attributes DROP CONSTRAINT IF EXISTS attributes_type_check");
        DB::statement("ALTER TABLE attributes ADD CONSTRAINT attributes_type_check CHECK (type IN ('text', 'number', 'select', 'boolean', 'textarea'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir al enum original
        DB::statement("UPDATE attributes SET type = 'text' WHERE type = 'textarea'");
        DB::statement("ALTER TABLE attributes DROP CONSTRAINT IF EXISTS attributes_type_check");
        DB::statement("ALTER TABLE attributes ADD CONSTRAINT attributes_type_check CHECK (type IN ('text', 'number', 'select', 'boolean'))");
    }
};
