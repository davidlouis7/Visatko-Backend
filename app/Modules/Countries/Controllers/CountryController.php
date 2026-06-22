<?php

namespace App\Modules\Countries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Requests\StoreCountryRequest;
use App\Modules\Countries\Requests\UpdateCountryRequest;
use App\Modules\Countries\Resources\CountryResource;
use App\Support\Http\ApiResponse;
use App\Support\Translations\SyncTranslations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CountryController extends Controller
{
    use ApiResponse;

    private const TRANSLATION_FIELDS = ['name', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keywords'];

    public function publicIndex(): JsonResponse
    {
        $items = Country::query()->where('is_active', true)->with(['translations.language', 'flag'])->orderBy('sort_order')->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, CountryResource::collection($items->getCollection()));
    }

    public function publicShow(string $slug): JsonResponse
    {
        $locale = request()->attributes->get('locale');
        $country = Country::query()->where('is_active', true)->whereHas('translations', fn ($query) => $query->where('slug', $slug)->whereHas('language', fn ($language) => $language->where('code', $locale)))->with(['translations.language', 'flag'])->firstOrFail();

        return $this->success(CountryResource::make($country));
    }

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Country::class);
        $items = Country::query()->with(['translations.language', 'flag'])->orderBy('sort_order')->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, CountryResource::collection($items->getCollection()));
    }

    public function store(StoreCountryRequest $request, SyncTranslations $sync): JsonResponse
    {
        $country = DB::transaction(function () use ($request, $sync): Country {
            $attributes = Arr::except($request->validated(), ['translations']);
            $attributes['code'] = strtoupper($attributes['code']);
            $country = Country::query()->create($attributes);
            $sync->execute($country, $request->validated('translations'), self::TRANSLATION_FIELDS);

            return $country;
        });
        activity('admin')->causedBy($request->user())->performedOn($country)->log('Country created');

        return $this->success(CountryResource::make($country->load(['translations.language', 'flag'])), 'Country created successfully.', 201);
    }

    public function update(UpdateCountryRequest $request, Country $country, SyncTranslations $sync): JsonResponse
    {
        DB::transaction(function () use ($request, $country, $sync): void {
            $attributes = Arr::except($request->validated(), ['translations']);
            if (isset($attributes['code'])) {
                $attributes['code'] = strtoupper($attributes['code']);
            }
            $country->update($attributes);
            if ($request->has('translations')) {
                $sync->execute($country, $request->validated('translations'), self::TRANSLATION_FIELDS);
            }
        });
        activity('admin')->causedBy($request->user())->performedOn($country)->log('Country updated');

        return $this->success(CountryResource::make($country->refresh()->load(['translations.language', 'flag'])), 'Country updated successfully.');
    }

    public function destroy(Country $country): JsonResponse
    {
        Gate::authorize('delete', $country);
        $country->delete();
        activity('admin')->causedBy(request()->user())->performedOn($country)->log('Country deleted');

        return $this->success(null, 'Country deleted successfully.');
    }
}
