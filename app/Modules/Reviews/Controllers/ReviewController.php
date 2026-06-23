<?php

namespace App\Modules\Reviews\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reviews\Models\Review;
use App\Modules\Reviews\Requests\StoreReviewRequest;
use App\Modules\Reviews\Requests\UpdateReviewRequest;
use App\Modules\Reviews\Resources\ReviewResource;
use App\Support\Cache\PublicApiCache;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    use ApiResponse;

    public function publicIndex(PublicApiCache $cache): JsonResponse
    {
        return $this->success($cache->remember('reviews.index', fn () => ReviewResource::collection(Review::query()->where('is_active', true)->orderBy('sort_order')->get())->resolve(request())));
    }

    public function featured(PublicApiCache $cache): JsonResponse
    {
        return $this->success($cache->remember('reviews.featured', fn () => ReviewResource::collection(Review::query()->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->get())->resolve(request())));
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Review::class);
        $items = Review::query()->latest()->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($items, ReviewResource::collection($items));
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        return $this->success(ReviewResource::make(Review::query()->create($request->validated())), 'Review created.', 201);
    }

    public function show(Review $review): JsonResponse
    {
        Gate::authorize('view', $review);

        return $this->success(ReviewResource::make($review));
    }

    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        $review->update($request->validated());

        return $this->success(ReviewResource::make($review), 'Review updated.');
    }

    public function destroy(Review $review): JsonResponse
    {
        Gate::authorize('delete', $review);
        $review->delete();

        return $this->success(null, 'Review deleted.');
    }
}
