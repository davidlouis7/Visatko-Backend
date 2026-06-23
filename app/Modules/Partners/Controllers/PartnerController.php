<?php

namespace App\Modules\Partners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Partners\Models\Partner;
use App\Modules\Partners\Requests\StorePartnerRequest;
use App\Modules\Partners\Requests\UpdatePartnerRequest;
use App\Modules\Partners\Resources\PartnerResource;
use App\Support\Cache\PublicApiCache;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PartnerController extends Controller
{
    use ApiResponse;

    public function publicIndex(PublicApiCache $cache): JsonResponse
    {
        return $this->success($cache->remember('partners.index', fn () => PartnerResource::collection(Partner::query()->where('is_active', true)->orderBy('sort_order')->get())->resolve(request())));
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Partner::class);
        $items = Partner::query()->orderBy('sort_order')->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($items, PartnerResource::collection($items));
    }

    public function store(StorePartnerRequest $request): JsonResponse
    {
        return $this->success(PartnerResource::make(Partner::query()->create($request->validated())), 'Partner created.', 201);
    }

    public function show(Partner $partner): JsonResponse
    {
        Gate::authorize('view', $partner);

        return $this->success(PartnerResource::make($partner));
    }

    public function update(UpdatePartnerRequest $request, Partner $partner): JsonResponse
    {
        $partner->update($request->validated());

        return $this->success(PartnerResource::make($partner), 'Partner updated.');
    }

    public function destroy(Partner $partner): JsonResponse
    {
        Gate::authorize('delete', $partner);
        $partner->delete();

        return $this->success(null, 'Partner deleted.');
    }
}
