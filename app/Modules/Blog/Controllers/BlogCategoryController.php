<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Requests\StoreBlogCategoryRequest;
use App\Modules\Blog\Requests\UpdateBlogCategoryRequest;
use App\Modules\Blog\Resources\BlogCategoryResource;
use App\Support\Http\ApiResponse;
use App\Support\Translations\SyncTranslations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BlogCategoryController extends Controller
{
    use ApiResponse;

    private const FIELDS = ['name', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keywords'];

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', BlogCategory::class);
        $items = BlogCategory::query()->with('translations.language')->orderBy('sort_order')->get();

        return $this->success(BlogCategoryResource::collection($items));
    }

    public function store(StoreBlogCategoryRequest $request, SyncTranslations $sync): JsonResponse
    {
        $item = DB::transaction(function () use ($request, $sync): BlogCategory {
            $item = BlogCategory::query()->create(Arr::except($request->validated(), 'translations'));
            $sync->execute($item, $request->validated('translations'), self::FIELDS);

            return $item;
        });
        activity('admin')->causedBy($request->user())->performedOn($item)->log('Blog category created');

        return $this->success(BlogCategoryResource::make($item->load('translations.language')), 'Blog category created successfully.', 201);
    }

    public function update(UpdateBlogCategoryRequest $request, BlogCategory $blogCategory, SyncTranslations $sync): JsonResponse
    {
        DB::transaction(function () use ($request, $blogCategory, $sync): void {
            $blogCategory->update(Arr::except($request->validated(), 'translations'));
            if ($request->has('translations')) {
                $sync->execute($blogCategory, $request->validated('translations'), self::FIELDS);
            }
        });
        activity('admin')->causedBy($request->user())->performedOn($blogCategory)->log('Blog category updated');

        return $this->success(BlogCategoryResource::make($blogCategory->refresh()->load('translations.language')), 'Blog category updated successfully.');
    }

    public function destroy(BlogCategory $blogCategory): JsonResponse
    {
        Gate::authorize('delete', $blogCategory);
        $blogCategory->delete();

        return $this->success(null, 'Blog category deleted successfully.');
    }
}
