<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductsInventory extends Pivot
{
    protected $table = 'products_inventory';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'inventory_id',
        'product_id',
        'product_quantity',
    ];
}
