<?php

namespace App\Modules\Branches\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Branches\Models\Branch;
use App\Modules\Branches\Requests\StoreBranchRequest;
use App\Modules\Branches\Requests\UpdateBranchRequest;
use App\Modules\Branches\Resources\BranchResource;
use App\Support\Cache\PublicApiCache;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BranchController extends Controller
{
    use ApiResponse;

    public function publicIndex(PublicApiCache $cache): JsonResponse
    {
        return $this->success($cache->remember('branches.index', fn () => BranchResource::collection(Branch::query()->where('is_active', true)->orderBy('sort_order')->get())->resolve(request())));
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Branch::class);
        $items = Branch::query()->orderBy('sort_order')->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($items, BranchResource::collection($items));
    }

    public function store(StoreBranchRequest $request): JsonResponse
    {
        return $this->success(BranchResource::make(Branch::query()->create($request->validated())), 'Branch created.', 201);
    }

    public function show(Branch $branch): JsonResponse
    {
        Gate::authorize('view', $branch);

        return $this->success(BranchResource::make($branch));
    }

    public function update(UpdateBranchRequest $request, Branch $branch): JsonResponse
    {
        $branch->update($request->validated());

        return $this->success(BranchResource::make($branch), 'Branch updated.');
    }

    public function destroy(Branch $branch): JsonResponse
    {
        Gate::authorize('delete', $branch);
        $branch->delete();

        return $this->success(null, 'Branch deleted.');
    }
}
