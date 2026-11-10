<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Funcion para mostrar todas las categorias que hay en la base de datos
     */
    public function indexCategories(): Collection {
        $categories = Category::all();

        return view('categories.index', ['Categorias' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createCategory(
        string $categoryName,
        string $description): Category {
        try {
            return Category::create([
                'Nombre' => $categoryName,
                'Descripcion' => $description,
            ]);

        } catch (\Exception $es) {
            // Manejar la excepción según sea necesario
            return response()->json(['error' => $es->getMessage()], 400);
        }

    }

    /**
     * Funcion para storear una nueva categoria en la base de datos
     */
    public function store(Request $request): Category {
       try {
            $request->validate([
                'Nombre' => 'required|string|max:255',
                'Descripcion' => 'nullable|string',
            ]);

            $category = Category::create($request->all());

        } catch (\Exception $es) {
            return response()->json(['error' => $es->getMessage()], 400);
        }
        return redirect()->route('categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Funcion para mostrar una categoria específica
     */
    public function showCategory(string $idCategory): ?Category {
        return Category::find($idCategory);
    }

    /**
     * Funcion para mostrar una categoria específica por nombre
     */
    public function showCategoryByName(string $categoryName): ?Category {
        return Category::where('NombreCategoria', $categoryName)->first();
    }

    /**
     * Funcion para editar una categoria específica
     */
    public function edit(string $idCategory): ?Category {
        $editCategory = $this->showCategory($idCategory);

        return view('categories.edit', [
            'Categoria' => $editCategory,
        ]);

    }

    /**
     * Funcion que actualiza una categoria específica
     */
    public function update(Request $request, string $idCategory): ?Category {
        try {
            $category = Category::find($idCategory);

        if (!$category) {
            throw new \RuntimeException('No se encontro la categoría.');
        }

        $category->NombreCategoria = $request->input('Nombre');
        $category->Descripcion = $request->input('Descripcion');

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada correctamente.');

        } catch (exception $er) {
            Log::error('Error al actualizar la categoría: ' . $er->getMessage());

            return redirect()->back()->withErrors(['error' => 'Error al actualizar la categoría.']);
        }
    }

    /**
     * Funcion para eliminar una categoria específica
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                throw new \RuntimeException('Categoría no encontrada.');
            }

            $category->delete();

            return redirect()->route('categories.index')->with('success', 'Categoría eliminada correctamente.');

        } catch (exception $er) {
            Log::error('Error al eliminar la categoría: ' . $er->getMessage());

            return redirect()->back()->withErrors(['error' => 'Error al eliminar la categoría.']);
        }
}
}
