<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Requests\StoreBlogPostRequest;
use App\Modules\Blog\Requests\UpdateBlogPostRequest;
use App\Modules\Blog\Resources\BlogPostResource;
use App\Support\Http\ApiResponse;
use App\Support\Translations\SyncTranslations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BlogPostController extends Controller
{
    use ApiResponse;

    private const FIELDS = ['title', 'slug', 'excerpt', 'content', 'meta_title', 'meta_description', 'meta_keywords'];

    private const RELATIONS = ['translations.language', 'author', 'category.translations.language', 'tags.translations.language', 'thumbnail', 'banner'];

    public function publicIndex(): JsonResponse
    {
        $items = BlogPost::query()->where('is_published', true)->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))->whereHas('category', fn ($q) => $q->where('is_active', true))->with(self::RELATIONS)->orderByDesc('published_at')->paginate(min((int) request('per_page', 15), 100));

        return $this->paginated($items, BlogPostResource::collection($items->getCollection()));
    }

    public function publicShow(string $slug): JsonResponse
    {
        $locale = request()->attributes->get('locale');
        $post = BlogPost::query()->where('is_published', true)->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))->whereHas('translations', fn ($q) => $q->where('slug', $slug)->whereHas('language', fn ($l) => $l->where('code', $locale)))->with(self::RELATIONS)->firstOrFail();

        return $this->success(BlogPostResource::make($post));
    }

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', BlogPost::class);
        $items = BlogPost::query()->with(self::RELATIONS)->latest()->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, BlogPostResource::collection($items->getCollection()));
    }

    public function store(StoreBlogPostRequest $request, SyncTranslations $sync): JsonResponse
    {
        $post = DB::transaction(function () use ($request, $sync): BlogPost {
            $attributes = Arr::except($request->validated(), ['translations', 'tag_ids']);
            $attributes['author_id'] ??= $request->user()->id;
            if (($attributes['is_published'] ?? false) && empty($attributes['published_at'])) {
                $attributes['published_at'] = now();
            } $post = BlogPost::query()->create($attributes);
            $sync->execute($post, $request->validated('translations'), self::FIELDS);
            $post->tags()->sync($request->input('tag_ids', []));

            return $post;
        });
        activity('admin')->causedBy($request->user())->performedOn($post)->log('Blog post created');

        return $this->success(BlogPostResource::make($post->load(self::RELATIONS)), 'Blog post created successfully.', 201);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost, SyncTranslations $sync): JsonResponse
    {
        DB::transaction(function () use ($request, $blogPost, $sync): void {
            $attributes = Arr::except($request->validated(), ['translations', 'tag_ids']);
            if (($attributes['is_published'] ?? false) && ! $blogPost->published_at && empty($attributes['published_at'])) {
                $attributes['published_at'] = now();
            } $blogPost->update($attributes);
            if ($request->has('translations')) {
                $sync->execute($blogPost, $request->validated('translations'), self::FIELDS);
            } if ($request->has('tag_ids')) {
                $blogPost->tags()->sync($request->input('tag_ids', []));
            }
        });
        activity('admin')->causedBy($request->user())->performedOn($blogPost)->log('Blog post updated');

        return $this->success(BlogPostResource::make($blogPost->refresh()->load(self::RELATIONS)), 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blogPost): JsonResponse
    {
        Gate::authorize('delete', $blogPost);
        $blogPost->delete();
        activity('admin')->causedBy(request()->user())->performedOn($blogPost)->log('Blog post deleted');

        return $this->success(null, 'Blog post deleted successfully.');
    }
}
