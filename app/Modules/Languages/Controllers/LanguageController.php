<?php

namespace App\Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Languages\Models\Language;
use App\Modules\Languages\Requests\StoreLanguageRequest;
use App\Modules\Languages\Requests\UpdateLanguageRequest;
use App\Modules\Languages\Resources\LanguageResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LanguageController extends Controller
{
    use ApiResponse;

    public function publicIndex(): JsonResponse
    {
        $languages = Language::query()->where('is_active', true)->orderBy('sort_order')->get();

        return $this->success(LanguageResource::collection($languages));
    }

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Language::class);
        $languages = Language::query()->orderBy('sort_order')->orderBy('name')->get();

        return $this->success(LanguageResource::collection($languages));
    }

    public function store(StoreLanguageRequest $request): JsonResponse
    {
        $language = DB::transaction(function () use ($request): Language {
            if ($request->boolean('is_default')) {
                Language::query()->update(['is_default' => false]);
            }

            return Language::query()->create($request->validated());
        });

        activity('admin')->causedBy($request->user())->performedOn($language)->log('Language created');

        return $this->success(LanguageResource::make($language), 'Language created successfully.', 201);
    }

    public function update(UpdateLanguageRequest $request, Language $language): JsonResponse
    {
        if ($language->is_default && ($request->has('is_default') && ! $request->boolean('is_default') || $request->has('is_active') && ! $request->boolean('is_active'))) {
            return $this->error('The default language must remain active and default.', 422);
        }

        DB::transaction(function () use ($request, $language): void {
            if ($request->boolean('is_default')) {
                Language::query()->whereKeyNot($language->getKey())->update(['is_default' => false]);
            }

            $language->update($request->validated());
        });

        activity('admin')->causedBy($request->user())->performedOn($language)->log('Language updated');

        return $this->success(LanguageResource::make($language->refresh()), 'Language updated successfully.');
    }

    public function destroy(Language $language): JsonResponse
    {
        Gate::authorize('delete', $language);
        $language->delete();
        activity('admin')->causedBy(request()->user())->performedOn($language)->log('Language deleted');

        return $this->success(null, 'Language deleted successfully.');
    }
}
