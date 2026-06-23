<?php

namespace App\Modules\CreditNotes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CreditNotes\Actions\CreateCreditNoteAction;
use App\Modules\CreditNotes\Actions\GenerateCreditNotePdfAction;
use App\Modules\CreditNotes\Actions\IssueCreditNoteAction;
use App\Modules\CreditNotes\Models\CreditNote;
use App\Modules\CreditNotes\Requests\StoreCreditNoteRequest;
use App\Modules\CreditNotes\Resources\CreditNoteResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CreditNoteController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', CreditNote::class);
        $paginator = CreditNote::query()->with('invoice')->latest()->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($paginator, CreditNoteResource::collection($paginator));
    }

    public function store(StoreCreditNoteRequest $request, CreateCreditNoteAction $action): JsonResponse
    {
        return $this->success(CreditNoteResource::make($action->execute($request->validated(), $request->user())), 'Credit note created.', 201);
    }

    public function show(CreditNote $creditNote): JsonResponse
    {
        Gate::authorize('view', $creditNote);

        return $this->success(CreditNoteResource::make($creditNote->load(['invoice', 'items'])));
    }

    public function issue(CreditNote $creditNote, IssueCreditNoteAction $action): JsonResponse
    {
        Gate::authorize('issue', $creditNote);

        return $this->success(CreditNoteResource::make($action->execute($creditNote, request()->user())), 'Credit note issued.');
    }

    public function pdf(CreditNote $creditNote, GenerateCreditNotePdfAction $action): Response
    {
        Gate::authorize('download', $creditNote);

        return $action->execute($creditNote)->stream($creditNote->credit_note_number.'.pdf');
    }
}
