<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\BlogTag;
use App\Modules\Blog\Requests\StoreBlogTagRequest;
use App\Modules\Blog\Requests\UpdateBlogTagRequest;
use App\Modules\Blog\Resources\BlogTagResource;
use App\Support\Http\ApiResponse;
use App\Support\Translations\SyncTranslations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BlogTagController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', BlogTag::class);

        return $this->success(BlogTagResource::collection(BlogTag::query()->with('translations.language')->get()));
    }

    public function store(StoreBlogTagRequest $request, SyncTranslations $sync): JsonResponse
    {
        $item = DB::transaction(function () use ($request, $sync): BlogTag {
            $item = BlogTag::query()->create(Arr::except($request->validated(), 'translations'));
            $sync->execute($item, $request->validated('translations'), ['name', 'slug']);

            return $item;
        });
        activity('admin')->causedBy($request->user())->performedOn($item)->log('Blog tag created');

        return $this->success(BlogTagResource::make($item->load('translations.language')), 'Blog tag created successfully.', 201);
    }

    public function update(UpdateBlogTagRequest $request, BlogTag $blogTag, SyncTranslations $sync): JsonResponse
    {
        DB::transaction(function () use ($request, $blogTag, $sync): void {
            $blogTag->update(Arr::except($request->validated(), 'translations'));
            if ($request->has('translations')) {
                $sync->execute($blogTag, $request->validated('translations'), ['name', 'slug']);
            }
        });
        activity('admin')->causedBy($request->user())->performedOn($blogTag)->log('Blog tag updated');

        return $this->success(BlogTagResource::make($blogTag->refresh()->load('translations.language')), 'Blog tag updated successfully.');
    }

    public function destroy(BlogTag $blogTag): JsonResponse
    {
        Gate::authorize('delete', $blogTag);
        $blogTag->delete();

        return $this->success(null, 'Blog tag deleted successfully.');
    }
}
