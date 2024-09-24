<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Get all categories of a type.
     *
     * @group Categories
     */
    public function index(CategoryType $type)
    {
        return CategoryResource::collection(Category::where('type', $type)->paginate());
    }

    /**
     * Create category
     *
     * @group Categories
     */
    public function store(Request $request, CategoryType $type)
    {
        Gate::authorize('create', Category::class);

        // TODO create category
    }

    /**
     * Get category by ID
     *
     * @group Categories
     */
    public function show(CategoryType $type, Category $category)
    {
        if ($category->type !== $type) {
            abort(404);
        }

        return new CategoryResource($category);
    }

    /**
     * Update category
     *
     * @group Categories
     */
    public function update(Request $request, CategoryType $type, Category $category)
    {
        if ($category->type !== $type) {
            abort(404);
        }
        Gate::authorize('update', $category);

        // TODO update category

        return new CategoryResource($category);
    }

    /**
     * Delete category
     *
     * @group Categories
     */
    public function destroy(CategoryType $type, Category $category)
    {
        if ($category->type !== $type) {
            abort(404);
        }
        Gate::authorize('delete', $category);

        $category->delete();

        return response()->noContent();
    }
}
