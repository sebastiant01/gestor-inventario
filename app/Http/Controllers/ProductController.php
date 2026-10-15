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
    public function indexProducts(): Collection {
        $products = Product::all();

        return view('products.index', ['Productos' => $products]);

    }

    /**
     * funcion para crear productos y devolverlos a la vista con los datos de la vista y el modelo, se procede a crear un nuevo producto.
     */
    public function createProduct(
        string $productName,
        float $productPrice,
        int $productStock,
        string $idCategory): Product {

        if (empty($productName) && trim($productName) === '') {
            throw new \InvalidArgumentException('El nombre del producto no puede estar vacío.');
        }
        if ($productPrice <= 0) {
            throw new \InvalidArgumentException('El precio debe ser mayor a cero.');
        }
        if ($productStock < 0) {
            throw new \InvalidArgumentException('El stock no puede ser negativo.');
        }

        $category = Category::where('idCategory', $idCategory)->first();

        //Es para corroborar que la categoria existe, si no existe lanza un error
        if (!$category) {
            throw new \RuntimeException('No se pudo crear o encontrar la categoría seleccionada.');
        }

        return Product::create([
            'Nombre' => $productName,
            'Precio' => $productPrice,
            'Stock' => $productStock,
            'Categoria' => $category->idCategory,
        ]);

    }

    /**
     * Funcion para storear un nuevo producto en la base de datos
     */
    public function store(Request $request): Product
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
            'Precio' => 'required|numeric|min:0',
            'Stock' => 'required|integer|min:0',
            'Categoria' => 'required|exists:categories,idCategory',
        ]);

        return Product::create($request->all());
    }

    /**
     * Funcion para mostrar un producto específico por id
     */
    public function showProduct(string $idProducto): ?Product{
        return Product::find($idProducto)->first();
    }

    /**
     * Funcion para editar un producto específico por id
     */
    public function edit(string $idProduct): ?Product {
        $editproduct = $this->showProduct($idProduct);


        return view('products.edit', ['producto' => $editproduct]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $idProduct): ?Product {
        try {
            $product = Product::find($idProduct);

        if (!$product) {
            throw new \RuntimeException('Producto no encontrado.');
        }

        $product->NombreProducto = $request->input('Nombre');
        $product->Precio = $request->input('Precio');
        $product->Stock = $request->input('Stock');
        $product->Categoria = $request->input('Categoria');

        $product->save();
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');

        } catch (exception $er) {
            Log::error('Error al actualizar el producto: ' . $er->getMessage());

        }


    }

    /**
     * Funcion para eliminar un producto específico por id
     */
    public function destroy(string $idProduct): ?Product
    {
        $product = Product::find($idProduct);

        if (!$product) {
            throw new \RuntimeException('Producto no encontrado.');
        }

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

        if (!$product) {
            throw new \RuntimeException('Producto no encontrado.');
        }

        $product->Stock = $newStock;
        $product->save();

        return $product;
    }
}
