<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use \Illuminate\Database\Eloquent\Collection;

class ProductController extends Controller
{
    /**
     * Muetsra todos los productos que haya en la base de datos y los devuelve a la vista
     */
    public function indexProducts(Request $request)
    {
        $products = Product::query()
            ->when($request->query('search'), function ($query, $search) {
                $query->where('product_name', 'like', "%{$search}%")
                      ->orWhere('product_id', 'like', "%{$search}%");
            })->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('products.index', compact('products'));

    }

    /**
     * funcion para crear productos y devolverlos a la vista con los datos de la vista y el modelo, se procede a crear un nuevo producto.
     */
    public function createProduct() {

        return view('products.create', ['editing' => false, 'product' => null]);

    }

    /**
     * Funcion para storear un nuevo producto en la base de datos
     */
    public function store(Request $request): Product
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string|max:200',
            'available_units' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'is_available' => 'required|boolean',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
        ]);

        $data['user_id'] = auth()->id(); // Asignar el ID del q inicio sesion

        return Product::create($data);
    }

    /**
     * Funcion para mostrar un producto específico por id
     */
    public function showProduct(string $idProducto): ?Product{
        return Product::findorfail($idProducto)->first();
    }

    /**
     * Funcion para editar un producto específico por id
     */
    public function editProduct(string $idProduct){
        $editproduct = Product::findorFail($idProduct);


        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProduct(Request $request, string $idProduct){
    try {
        $product = Product::findorFail($idProduct);

        $product->product_name = $request->input('product_name');
        $product->product_price = $request->input('product_price');
        $product->product_description = $request->input('product_description');
        $product->available_units = $request->input('available_units');
        $product->category_id = $request->input('category_id');
        $product->is_available = $request->input('is_available');
        $product->supplier_id = $request->input('supplier_id');

        $product->save();
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');

        } catch (exception $er) {
            Log::error('Error al actualizar el producto: ' . $er->getMessage());

        }


    }

    /**
     * Funcion para eliminar un producto específico por id
     */
    public function destroyProduct(string $idProduct): ?Product
    {
        $product = Product::findorfail($idProduct);


        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Funcion para obtener todos los productos de una categoria específica
     */

    public function getProductsByCategory(string $idCategory): Collection
    {
        return Product::where('Categoria', $idCategory)->get();
    }

    /**
     * Funcion para actualizar el stock de un producto
     */

    public function UpdateStock(string $idProduct, int $newStock): ?Product
    {
        $product = Product::find($idProduct);

        $product->Stock = $newStock;
        $product->save();

        return $product;
    }
}
