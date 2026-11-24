<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display all categories from the database.
     */
    public function indexCategories(): View {
        $categories = Category::all();

        return view('categories.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createCategory(): View {

        return view('categories.create');
    }

    /**
     * Store a newly created category in the database.
     */
    public function store(Request $request): Category | JsonResponse | RedirectResponse  {
        Validator::make($request->all(),
        [
            'category_name' => 'required|string|unique:categories,category_name',
            'category_description' => 'nullable|string',
        ],
        [
            'category_name.required' => 'The category name is required.',
            'category_name.unique' => 'The category name already exists.',
        ])->validate();

       try {
            Category::create($request->all());

        } catch (\Exception $es) {
            return response()->json(['error' => $es->getMessage()], 400);
        }
        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display a specific category.
     */
    public function showCategory(string $idCategory): ?Category {
        return Category::find($idCategory);
    }

    /**
     * Display a specific category by name.
     */
    public function showCategoryByName(string $categoryName): ?Category {
        return Category::where('category_name', $categoryName)->first();
    }

    /**
     * Show the form for editing a specific category.
     */
    public function edit(string $idCategory): View | RedirectResponse {
        $editCategory = $this->showCategory($idCategory);

        if (empty($editCategory)) {

            Session::flash('message', [ 'content' => "Category with id '$idCategory' doesn't exist.", 'type' => 'error' ]);
            return redirect()->back();
        }

        return view('categories.edit', [
            'category' => $editCategory,
        ]);

    }

    /**
     * Update a specific category in the database.
     */
    public function update(Request $request): Category | RedirectResponse {
        Validator::make($request->all(),
        [
            'category_name' => 'required|string|unique:categories,category_name,' . $request->category_id . ',category_id',
            'category_description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
        ],
        [
            'category_name.required' => 'The category name is required.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The given id does not exist.',
        ])->validate();

        try {
            $category = Category::find($request->category_id);

            $category->category_name = $request->category_name;
            $category->category_description = $request->category_description;
            $category->save();

            return redirect()->route('categories.index')->with('success', 'Category updated successfully.');

        } catch (Exception $er) {
            Log::error('Error updating category: ' . $er->getMessage());

            return redirect()->back()->withErrors(['error' => 'Error updating category.']);
        }
    }

    /**
     * Remove a specific category from the database.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                Session::flash('message', ['content' => "Category with id '$id' doesn't exist.", 'type' => 'error']);
                return redirect()->back();
            }

            $category->delete();

            return redirect()->route('categories.index')->with('success', 'Category successfully deleted.');

        } catch (Exception $er) {
            Log::error('Error deleting category: ' . $er->getMessage());

            return redirect()->back()->withErrors(['error' => 'Error deleting category.']);
        }
}
}
