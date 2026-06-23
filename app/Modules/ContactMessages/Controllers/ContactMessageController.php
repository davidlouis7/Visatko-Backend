<?php

namespace App\Modules\ContactMessages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ContactMessages\Enums\ContactMessageStatus;
use App\Modules\ContactMessages\Events\ContactMessageCreated;
use App\Modules\ContactMessages\Models\ContactMessage;
use App\Modules\ContactMessages\Requests\StoreContactMessageRequest;
use App\Modules\ContactMessages\Requests\UpdateContactMessageRequest;
use App\Modules\ContactMessages\Resources\ContactMessageResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContactMessageController extends Controller
{
    use ApiResponse;

    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::query()->create([...$request->validated(), 'status' => ContactMessageStatus::New, 'source' => $request->input('source', 'website'), 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
        event(new ContactMessageCreated($message));

        return $this->success(ContactMessageResource::make($message), 'Contact message submitted.', 201);
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ContactMessage::class);
        $items = ContactMessage::query()->latest()->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($items, ContactMessageResource::collection($items));
    }

    public function show(ContactMessage $contactMessage): JsonResponse
    {
        Gate::authorize('view', $contactMessage);

        return $this->success(ContactMessageResource::make($contactMessage));
    }

    public function update(UpdateContactMessageRequest $request, ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->update($request->validated());

        return $this->success(ContactMessageResource::make($contactMessage), 'Contact message updated.');
    }

    public function assign(Request $request, ContactMessage $contactMessage): JsonResponse
    {
        abort_unless($request->user()?->can('contact_messages.assign'), 403);
        $data = $request->validate(['assigned_to' => ['required', 'exists:users,id']]);
        $contactMessage->update($data);

        return $this->success(ContactMessageResource::make($contactMessage), 'Contact message assigned.');
    }

    public function markRead(Request $request, ContactMessage $contactMessage): JsonResponse
    {
        abort_unless($request->user()?->can('contact_messages.update'), 403);
        $contactMessage->update(['status' => ContactMessageStatus::Read]);

        return $this->success(ContactMessageResource::make($contactMessage), 'Contact message marked read.');
    }

    public function close(Request $request, ContactMessage $contactMessage): JsonResponse
    {
        abort_unless($request->user()?->can('contact_messages.close'), 403);
        $contactMessage->update(['status' => ContactMessageStatus::Closed]);

        return $this->success(ContactMessageResource::make($contactMessage), 'Contact message closed.');
    }
}
