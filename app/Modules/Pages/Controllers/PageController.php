<?php

namespace App\Modules\Pages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Pages\Models\Page;
use App\Modules\Pages\Requests\StorePageRequest;
use App\Modules\Pages\Requests\UpdatePageRequest;
use App\Modules\Pages\Resources\PageResource;
use App\Support\Http\ApiResponse;
use App\Support\Translations\SyncTranslations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PageController extends Controller
{
    use ApiResponse;

    private const FIELDS = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'meta_keywords'];

    public function publicIndex(): JsonResponse
    {
        return $this->success(PageResource::collection(Page::query()->where('is_active', true)->with('translations.language')->get()));
    }

    public function publicShow(string $slug): JsonResponse
    {
        $locale = request()->attributes->get('locale');
        $page = Page::query()->where('is_active', true)->whereHas('translations', fn ($q) => $q->where('slug', $slug)->whereHas('language', fn ($l) => $l->where('code', $locale)))->with('translations.language')->firstOrFail();

        return $this->success(PageResource::make($page));
    }

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Page::class);

        return $this->success(PageResource::collection(Page::query()->with('translations.language')->get()));
    }

    public function store(StorePageRequest $request, SyncTranslations $sync): JsonResponse
    {
        $page = DB::transaction(function () use ($request, $sync): Page {
            $page = Page::query()->create(Arr::except($request->validated(), 'translations'));
            $sync->execute($page, $request->validated('translations'), self::FIELDS);

            return $page;
        });
        activity('admin')->causedBy($request->user())->performedOn($page)->log('Page created');

        return $this->success(PageResource::make($page->load('translations.language')), 'Page created successfully.', 201);
    }

    public function update(UpdatePageRequest $request, Page $page, SyncTranslations $sync): JsonResponse
    {
        DB::transaction(function () use ($request, $page, $sync): void {
            $page->update(Arr::except($request->validated(), 'translations'));
            if ($request->has('translations')) {
                $sync->execute($page, $request->validated('translations'), self::FIELDS);
            }
        });
        activity('admin')->causedBy($request->user())->performedOn($page)->log('Page updated');

        return $this->success(PageResource::make($page->refresh()->load('translations.language')), 'Page updated successfully.');
    }

    public function destroy(Page $page): JsonResponse
    {
        Gate::authorize('delete', $page);
        $page->delete();
        activity('admin')->causedBy(request()->user())->performedOn($page)->log('Page deleted');

        return $this->success(null, 'Page deleted successfully.');
    }
}
