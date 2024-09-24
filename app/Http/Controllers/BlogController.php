<?php

namespace App\Http\Controllers;

use App\Filters\BlogFilter;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BlogController extends Controller
{
    /**
     * Get all blogs
     *
     * @group Blogs
     */
    public function index(BlogFilter $filter)
    {
        return BlogResource::collection(Blog::with(['category'])->filter($filter)->paginate($this->getPerPage()));
    }

    /**
     * Create blog
     *
     * @group Blogs
     */
    public function store(Request $request) {}

    /**
     * Get blog by ID
     *
     * @group Blogs
     */
    public function show(Blog $blog)
    {
        return new BlogResource($blog);
    }

    /**
     * Update blog
     *
     * @group Blogs
     */
    public function update(Request $request, Blog $blog) {}

    /**
     * Delete blog
     *
     * @group Blogs
     */
    public function destroy(Blog $blog)
    {
        Gate::authorize('delete', $blog);

        $blog->delete();

        return response()->noContent();
    }
}
