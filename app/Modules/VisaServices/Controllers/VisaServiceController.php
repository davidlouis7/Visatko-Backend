<?php

namespace App\Modules\VisaServices\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VisaServices\Models\VisaService;
use App\Modules\VisaServices\Requests\StoreVisaServiceRequest;
use App\Modules\VisaServices\Requests\UpdateVisaServiceRequest;
use App\Modules\VisaServices\Resources\VisaServiceResource;
use App\Support\Http\ApiResponse;
use App\Support\Translations\SyncTranslations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class VisaServiceController extends Controller
{
    use ApiResponse;

    private const TRANSLATION_FIELDS = ['title', 'slug', 'short_description', 'full_description', 'requirements', 'required_documents', 'terms_conditions', 'meta_title', 'meta_description', 'meta_keywords'];

    private const RELATIONS = ['translations.language', 'country.translations.language', 'thumbnail', 'banner', 'gallery'];

    public function publicIndex(): JsonResponse
    {
        return $this->publicList(false);
    }

    public function featured(): JsonResponse
    {
        return $this->publicList(true);
    }

    private function publicList(bool $featured): JsonResponse
    {
        $query = VisaService::query()->where('is_active', true)->when($featured, fn ($q) => $q->where('is_featured', true))->with(self::RELATIONS)->orderBy('sort_order');
        if (request('country_id')) {
            $query->where('country_id', request('country_id'));
        }
        $items = $query->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, VisaServiceResource::collection($items->getCollection()));
    }

    public function publicShow(string $slug): JsonResponse
    {
        $locale = request()->attributes->get('locale');
        $service = VisaService::query()->where('is_active', true)->whereHas('translations', fn ($query) => $query->where('slug', $slug)->whereHas('language', fn ($language) => $language->where('code', $locale)))->with(self::RELATIONS)->firstOrFail();

        return $this->success(VisaServiceResource::make($service));
    }

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', VisaService::class);
        $items = VisaService::query()->with(self::RELATIONS)->orderBy('sort_order')->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, VisaServiceResource::collection($items->getCollection()));
    }

    public function store(StoreVisaServiceRequest $request, SyncTranslations $sync): JsonResponse
    {
        $service = DB::transaction(function () use ($request, $sync): VisaService {
            $service = VisaService::query()->create(Arr::except($request->validated(), ['translations', 'gallery_media_ids']));
            $sync->execute($service, $request->validated('translations'), self::TRANSLATION_FIELDS);
            $this->syncGallery($service, $request->input('gallery_media_ids', []));

            return $service;
        });
        activity('admin')->causedBy($request->user())->performedOn($service)->log('Visa service created');

        return $this->success(VisaServiceResource::make($service->load(self::RELATIONS)), 'Visa service created successfully.', 201);
    }

    public function update(UpdateVisaServiceRequest $request, VisaService $visaService, SyncTranslations $sync): JsonResponse
    {
        DB::transaction(function () use ($request, $visaService, $sync): void {
            $visaService->update(Arr::except($request->validated(), ['translations', 'gallery_media_ids']));
            if ($request->has('translations')) {
                $sync->execute($visaService, $request->validated('translations'), self::TRANSLATION_FIELDS);
            }
            if ($request->has('gallery_media_ids')) {
                $this->syncGallery($visaService, $request->input('gallery_media_ids', []));
            }
        });
        activity('admin')->causedBy($request->user())->performedOn($visaService)->log('Visa service updated');

        return $this->success(VisaServiceResource::make($visaService->refresh()->load(self::RELATIONS)), 'Visa service updated successfully.');
    }

    public function destroy(VisaService $visaService): JsonResponse
    {
        Gate::authorize('delete', $visaService);
        $visaService->delete();
        activity('admin')->causedBy(request()->user())->performedOn($visaService)->log('Visa service deleted');

        return $this->success(null, 'Visa service deleted successfully.');
    }

    private function syncGallery(VisaService $service, array $ids): void
    {
        $service->gallery()->sync(collect($ids)->mapWithKeys(fn (int $id, int $index): array => [$id => ['sort_order' => $index]])->all());
    }
}
