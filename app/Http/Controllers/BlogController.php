<?php

namespace App\Http\Controllers;

use App\Actions\SaveBlog;
use App\Enums\BlogStatus;
use App\Filters\BlogFilter;
use App\Http\Requests\CreateOrUpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Database\Eloquent\Builder;
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
        if (request()->routeIs('public.blogs.index')) {
            request()->query->add(['status' => BlogStatus::PUBLISHED->value]);
        }

        return BlogResource::collection(Blog::with(['latestAudit.user', 'tags'])->filter($filter)->paginate($this->getPerPage()));
    }

    /**
     * Create blog
     *
     * @group Blogs
     */
    public function store(CreateOrUpdateBlogRequest $request, SaveBlog $saveBlog)
    {
        Gate::authorize('create', Blog::class);

        $blog = $saveBlog->execute(new Blog, $request->validated());

        return new BlogResource($blog);
    }

    /**
     * Get blog by ID
     *
     * @group Blogs
     */
    public function show(Blog $blog)
    {
        $otherBlogs = [];
        $missingBlog = null;
        if (request()->routeIs('public.blogs.show')) {
            $otherBlogs = Blog::whereHas('tags', function (Builder $query) use ($blog) {
                $tagIds = collect($blog->tags)->pluck('id');
                $query->whereIn('tags.id', $tagIds);
            })->with(['latestAudit.user', 'tags'])
                ->whereNot('id', $blog->id)
                ->where('published_at', '<=', now())
                ->orderByDesc('created_at')
                ->limit(3)->get();
            if ($otherBlogs->count() < 3) {
                $blogCount = 3 - $otherBlogs->count();
                $missingBlog = Blog::with(['latestAudit.user', 'tags'])
                    ->whereNot('id', $blog->id)
                    ->where('published_at', '<=', now())
                    ->orderByDesc('created_at')
                    ->limit($blogCount)->get();
            }
        }

        return (new BlogResource($blog))
            ->additional(request()->routeIs('public.blogs.show') ? ['other_blogs' => BlogResource::collection($otherBlogs->merge($missingBlog ?? []))] : []);
    }

    /**
     * Update blog
     *
     * @group Blogs
     */
    public function update(CreateOrUpdateBlogRequest $request, Blog $blog, SaveBlog $saveBlog)
    {
        Gate::authorize('update', $blog);

        $blog = $saveBlog->execute($blog, $request->validated());

        return new BlogResource($blog);
    }

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
