<?php

namespace App\Modules\Emails\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Emails\Models\EmailTemplate;
use App\Modules\Emails\Requests\UpdateEmailTemplateRequest;
use App\Modules\Emails\Resources\EmailTemplateResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmailTemplateController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', EmailTemplate::class);
        $templates = EmailTemplate::query()->orderBy('key')->paginate((int) $request->integer('per_page', 50));

        return $this->paginated($templates, EmailTemplateResource::collection($templates));
    }

    public function show(EmailTemplate $template): JsonResponse
    {
        Gate::authorize('view', $template);

        return $this->success(EmailTemplateResource::make($template));
    }

    public function update(UpdateEmailTemplateRequest $request, EmailTemplate $template): JsonResponse
    {
        $template->update($request->validated());
        activity('admin')->causedBy($request->user())->performedOn($template)->log('Email template updated');

        return $this->success(EmailTemplateResource::make($template->refresh()), 'Email template updated.');
    }
}
