<?php

namespace App\Modules\Counters\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Counters\Models\Counter;
use App\Modules\Counters\Requests\StoreCounterRequest;
use App\Modules\Counters\Requests\UpdateCounterRequest;
use App\Modules\Counters\Resources\CounterResource;
use App\Support\Cache\PublicApiCache;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CounterController extends Controller
{
    use ApiResponse;

    public function publicIndex(PublicApiCache $cache): JsonResponse
    {
        return $this->success($cache->remember('counters.index', fn () => CounterResource::collection(Counter::query()->where('is_active', true)->orderBy('sort_order')->get())->resolve(request())));
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Counter::class);
        $items = Counter::query()->orderBy('sort_order')->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($items, CounterResource::collection($items));
    }

    public function store(StoreCounterRequest $request): JsonResponse
    {
        return $this->success(CounterResource::make(Counter::query()->create($request->validated())), 'Counter created.', 201);
    }

    public function show(Counter $counter): JsonResponse
    {
        Gate::authorize('view', $counter);

        return $this->success(CounterResource::make($counter));
    }

    public function update(UpdateCounterRequest $request, Counter $counter): JsonResponse
    {
        $counter->update($request->validated());

        return $this->success(CounterResource::make($counter), 'Counter updated.');
    }

    public function destroy(Counter $counter): JsonResponse
    {
        Gate::authorize('delete', $counter);
        $counter->delete();

        return $this->success(null, 'Counter deleted.');
    }
}
