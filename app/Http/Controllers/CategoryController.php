<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories of a type.
     *
     * @group Categories
     */
    public function index(CategoryType $type)
    {
        return Category::where('type', $type)->get();
    }

    /**
     * Create category
     *
     * @group Categories
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Get category by ID
     *
     * @group Categories
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update category
     *
     * @group Categories
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Delete category
     *
     * @group Categories
     */
    public function destroy(Category $category)
    {
        //
    }
}
