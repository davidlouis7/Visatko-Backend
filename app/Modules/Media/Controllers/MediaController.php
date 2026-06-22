<?php

namespace App\Modules\Media\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Requests\UploadMediaRequest;
use App\Modules\Media\Resources\MediaResource;
use App\Modules\Media\Services\FileUploadService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Media::class);
        $media = Media::query()->latest()->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($media, MediaResource::collection($media->getCollection()));
    }

    public function store(UploadMediaRequest $request, FileUploadService $service): JsonResponse
    {
        $media = $service->upload(
            $request->file('file'),
            $request->string('collection')->value(),
            $request->user(),
            $request->input('metadata', []),
        );

        activity('admin')->causedBy($request->user())->performedOn($media)->log('Media uploaded');

        return $this->success(MediaResource::make($media), 'File uploaded successfully.', 201);
    }

    public function download(Media $media): StreamedResponse
    {
        Gate::authorize('view', $media);

        return Storage::disk($media->disk)->download($media->path, $media->original_name);
    }

    public function destroy(Media $media, FileUploadService $service): JsonResponse
    {
        Gate::authorize('delete', $media);
        $actor = request()->user();
        $service->delete($media);
        activity('admin')->causedBy($actor)->withProperties(['media_id' => $media->public_id])->log('Media deleted');

        return $this->success(null, 'File deleted successfully.');
    }
}
