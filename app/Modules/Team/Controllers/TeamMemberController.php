<?php

namespace App\Modules\Team\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Team\Models\TeamMember;
use App\Modules\Team\Requests\StoreTeamMemberRequest;
use App\Modules\Team\Requests\UpdateTeamMemberRequest;
use App\Modules\Team\Resources\TeamMemberResource;
use App\Support\Cache\PublicApiCache;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamMemberController extends Controller
{
    use ApiResponse;

    public function publicIndex(PublicApiCache $cache): JsonResponse
    {
        return $this->success($cache->remember('team-members.index', fn () => TeamMemberResource::collection(TeamMember::query()->where('is_active', true)->orderBy('sort_order')->get())->resolve(request())));
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', TeamMember::class);
        $items = TeamMember::query()->orderBy('sort_order')->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($items, TeamMemberResource::collection($items));
    }

    public function store(StoreTeamMemberRequest $request): JsonResponse
    {
        return $this->success(TeamMemberResource::make(TeamMember::query()->create($request->validated())), 'Team member created.', 201);
    }

    public function show(TeamMember $teamMember): JsonResponse
    {
        Gate::authorize('view', $teamMember);

        return $this->success(TeamMemberResource::make($teamMember));
    }

    public function update(UpdateTeamMemberRequest $request, TeamMember $teamMember): JsonResponse
    {
        $teamMember->update($request->validated());

        return $this->success(TeamMemberResource::make($teamMember), 'Team member updated.');
    }

    public function destroy(TeamMember $teamMember): JsonResponse
    {
        Gate::authorize('delete', $teamMember);
        $teamMember->delete();

        return $this->success(null, 'Team member deleted.');
    }
}
