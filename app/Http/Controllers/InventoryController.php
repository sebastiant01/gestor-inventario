<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $inventories = Inventory::with('products')->get();

        return view('inventories.index', ['inventories' => $inventories]);
    }

    public function create(): View
    {
        return view('inventories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'inventory_name' => 'required|string|unique:inventories,inventory_name',
            'capacity' => 'required|integer|min:1',
        ],
        [
            'inventory_name.required' => 'The inventory name is required.',
            'inventory_name.unique' => 'The inventory name already exists.',
            'capacity.required' => 'The capacity is required.',
            'capacity.min' => 'The capacity must be greater than zero.',
        ])->validate();

        try {
            $inventory = new Inventory();
            $inventory->inventory_name = $request->inventory_name;
            $inventory->capacity = $request->capacity;
            $inventory->total_units = 0;
            $inventory->is_active = true;
            $inventory->user_id = Auth::id();
            $inventory->save();

            return redirect()->route('inventories.index')->with('success', 'Inventory created successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function edit(string $id): View | RedirectResponse
    {
        $inventory = Inventory::find($id);

        if (empty($inventory)) {
            return redirect()->back()->withErrors(['error' => "Inventory with id '$id' doesn't exist."]);
        }

        return view('inventories.edit', ['inventory' => $inventory]);
    }

    public function update(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'inventory_id' => 'required|exists:inventories,inventory_id',
            'inventory_name' => 'required|string|unique:inventories,inventory_name,' . $request->inventory_id . ',inventory_id',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ],
        [
            'inventory_id.required' => 'The inventory is required.',
            'inventory_id.exists' => 'The given id does not exist.',
            'inventory_name.required' => 'The inventory name is required.',
            'inventory_name.unique' => 'The inventory name already exists.',
            'capacity.required' => 'The capacity is required.',
            'capacity.min' => 'The capacity must be greater than zero.',
        ])->validate();

        try {
            $inventory = Inventory::find($request->inventory_id);

            if ($request->capacity < $inventory->total_units) {
                return redirect()->back()->withErrors([
                    'error' => "The new capacity ($request->capacity) cannot be less than the current total units ($inventory->total_units)."
                ]);
            }

            $inventory->inventory_name = $request->inventory_name;
            $inventory->capacity = $request->capacity;
            $inventory->is_active = $request->boolean('is_active');
            $inventory->save();

            return redirect()->route('inventories.index')->with('success', 'Inventory updated successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function delete(string $id): RedirectResponse
    {
        try {
            $inventory = Inventory::find($id);

            if (empty($inventory)) {
                return redirect()->back()->withErrors(['error' => "Inventory with id '$id' doesn't exist."]);
            }

            $inventory->delete();

            return redirect()->route('inventories.index')->with('success', 'Inventory deleted successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function addProduct(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'inventory_id' => 'required|exists:inventories,inventory_id',
            'product_id' => 'required|exists:products,product_id',
            'product_quantity' => 'required|integer|min:1',
        ],
        [
            'inventory_id.required' => 'The inventory is required.',
            'inventory_id.exists' => 'The selected inventory does not exist.',
            'product_id.required' => 'The product is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'product_quantity.required' => 'The product quantity is required.',
            'product_quantity.min' => 'The product quantity must be greater than zero.',
        ])->validate();

        try {
            $inventory = Inventory::find($request->inventory_id);
            $product = Product::find($request->product_id);

            if (($inventory->total_units + $request->product_quantity) > $inventory->capacity) {
                return redirect()->back()->withErrors([
                    'error' => "Capacity exceeded. Available space: " . ($inventory->capacity - $inventory->total_units) . " units."
                ]);
            }

            if ($product->available_units < $request->product_quantity) {
                return redirect()->back()->withErrors([
                    'error' => "Not enough stock. Available units for this product: $product->available_units."
                ]);
            }

            DB::transaction(function () use ($inventory, $product, $request) {
                $inventory->products()->attach($request->product_id, [
                    'product_quantity' => $request->product_quantity,
                ]);

                $inventory->total_units += $request->product_quantity;
                $inventory->save();

                $product->available_units -= $request->product_quantity;
                $product->save();
            });

            return redirect()->route('inventories.index')->with('success', 'Product added to inventory successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function removeProduct(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'inventory_id' => 'required|exists:inventories,inventory_id',
            'product_id' => 'required|exists:products,product_id',
        ],
        [
            'inventory_id.required' => 'The inventory is required.',
            'inventory_id.exists' => 'The selected inventory does not exist.',
            'product_id.required' => 'The product is required.',
            'product_id.exists' => 'The selected product does not exist.',
        ])->validate();

        try {
            $inventory = Inventory::find($request->inventory_id);
            $product = Product::find($request->product_id);

            $removedQuantity = $inventory->products()
                ->where('product_id', $request->product_id)
                ->first()
                ->pivot->product_quantity;

            DB::transaction(function () use ($inventory, $product, $request, $removedQuantity) {
                $inventory->products()->detach($request->product_id);

                $inventory->total_units -= $removedQuantity;
                $inventory->save();

                $product->available_units += $removedQuantity;
                $product->save();
            });

            return redirect()->route('inventories.index')->with('success', 'Product removed from inventory successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function updateProductQuantity(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'inventory_id' => 'required|exists:inventories,inventory_id',
            'product_id' => 'required|exists:products,product_id',
            'product_quantity' => 'required|integer|min:0',
        ],
        [
            'inventory_id.required' => 'The inventory is required.',
            'inventory_id.exists' => 'The selected inventory does not exist.',
            'product_id.required' => 'The product is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'product_quantity.required' => 'The product quantity is required.',
            'product_quantity.min' => 'The product quantity cannot be negative.',
        ])->validate();

        try {
            $inventory = Inventory::find($request->inventory_id);
            $product = Product::find($request->product_id);

            $currentQuantity = $inventory->products()
                ->where('product_id', $request->product_id)
                ->first()
                ->pivot->product_quantity;

            $difference = $request->product_quantity - $currentQuantity;

            if (($inventory->total_units + $difference) > $inventory->capacity) {
                return redirect()->back()->withErrors([
                    'error' => "Capacity exceeded. Available space: " . ($inventory->capacity - $inventory->total_units) . " units."
                ]);
            }

            if ($difference > 0 && $product->available_units < $difference) {
                return redirect()->back()->withErrors([
                    'error' => "Not enough stock. Available units for this product: $product->available_units."
                ]);
            }

            DB::transaction(function () use ($inventory, $product, $request, $difference) {
                $inventory->products()->updateExistingPivot($request->product_id, [
                    'product_quantity' => $request->product_quantity,
                ]);

                $inventory->total_units += $difference;
                $inventory->save();

                $product->available_units -= $difference;
                $product->save();
            });

            return redirect()->route('inventories.index')->with('success', 'Product quantity updated successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }
}
