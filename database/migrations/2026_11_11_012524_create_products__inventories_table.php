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
        Schema::create('products_inventory', function (Blueprint $table) {
            $table->foreignId('inventory_id')
                ->constrained('inventories', 'inventory_id');

            $table->foreignId('product_id')
                ->constrained('products', 'product_id');

            $table->integer('product_quantity');

            $table->primary(['inventory_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products__inventories');
    }
};
