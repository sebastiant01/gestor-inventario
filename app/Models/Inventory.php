<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Inventory extends Model
{
    protected $primaryKey = 'inventory_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'products_inventory',
            'inventory_id',
            'product_id',
            'inventory_id',
            'product_id'
        )->using(ProductsInventory::class)
        ->withPivot('product_quantity');
    }
}
