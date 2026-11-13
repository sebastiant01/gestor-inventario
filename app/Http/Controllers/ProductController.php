<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'supplier'])->get();

        return view('products.index', ['products' => $products]);
    }

    public function create(): View
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('products.create', [
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'product_name' => 'required|string|unique:products,product_name',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string',
            'available_units' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
        ],
        [
            'product_name.required' => 'The product name is required.',
            'product_name.unique' => 'The product name already exists.',
            'product_price.required' => 'The product price is required.',
            'product_price.min' => 'The product price must be greater than zero.',
            'available_units.required' => 'The available units are required.',
            'available_units.min' => 'The available units cannot be negative.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'supplier_id.required' => 'The supplier is required.',
            'supplier_id.exists' => 'The selected supplier does not exist.',
        ])->validate();

        try {
            $product = new Product();
            $product->product_name = $request->product_name;
            $product->product_price = $request->product_price;
            $product->product_description = $request->product_description;
            $product->available_units = $request->available_units;
            $product->is_available = true;
            $product->category_id = $request->category_id;
            $product->supplier_id = $request->supplier_id;
            $product->user_id = Auth::id();
            $product->save();

            return redirect()->route('products.index')->with('success', 'Product created successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function edit(string $id): View | RedirectResponse
    {
        $product = Product::find($id);

        if (empty($product)) {
            return redirect()->back()->withErrors(['error' => "Product with id '$id' doesn't exist."]);
        }

        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'product_id' => 'required|exists:products,product_id',
            'product_name' => 'required|string|unique:products,product_name,' . $request->product_id . ',product_id',
            'product_price' => 'required|numeric|min:0.01',
            'product_description' => 'nullable|string',
            'available_units' => 'required|integer|min:1',
            'is_available' => 'boolean',
            'category_id' => 'required|exists:categories,category_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
        ],
        [
            'product_id.required' => 'The product is required.',
            'product_id.exists' => 'The given id does not exist.',
            'product_name.required' => 'The product name is required.',
            'product_name.unique' => 'The product name already exists.',
            'product_price.required' => 'The product price is required.',
            'product_price.min' => 'The product price must be greater than zero.',
            'available_units.required' => 'The available units are required.',
            'available_units.min' => 'The available units cannot be negative.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'supplier_id.required' => 'The supplier is required.',
            'supplier_id.exists' => 'The selected supplier does not exist.',
        ])->validate();

        try {
            $product = Product::find($request->product_id);
            $product->product_name = $request->product_name;
            $product->product_price = $request->product_price;
            $product->product_description = $request->product_description;
            $product->available_units = $request->available_units;
            $product->is_available = $request->boolean('is_available', true);
            $product->category_id = $request->category_id;
            $product->supplier_id = $request->supplier_id;
            $product->save();

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function delete(string $id): RedirectResponse
    {
        try {
            $product = Product::find($id);

            if (empty($product)) {
                return redirect()->back()->withErrors(['error' => "Product with id '$id' doesn't exist."]);
            }

            $product->delete();

            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function getByCategory(string $categoryId): View | RedirectResponse
    {
        $category = Category::find($categoryId);

        if (empty($category)) {
            return redirect()->back()->withErrors(['error' => "Category with id '$categoryId' doesn't exist."]);
        }

        $products = Product::where('category_id', $categoryId)->get();

        return view('products.index', ['products' => $products]);
    }
}
