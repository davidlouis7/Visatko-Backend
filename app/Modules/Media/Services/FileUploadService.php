<?php

namespace App\Modules\Media\Services;

use App\Models\User;
use App\Modules\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FileUploadService
{
    private const PUBLIC_COLLECTIONS = [
        'service_images', 'blog_images', 'team_images', 'partner_logos',
        'review_images', 'branch_images',
    ];

    public function upload(UploadedFile $file, string $collection, ?User $uploader, array $metadata = []): Media
    {
        $isPublic = in_array($collection, self::PUBLIC_COLLECTIONS, true);
        $disk = $isPublic ? 'public' : 'local';
        $visibility = $isPublic ? 'public' : 'private';
        $extension = strtolower($file->extension() ?: $file->guessExtension() ?: 'bin');
        $path = $collection.'/'.now()->format('Y/m').'/'.Str::uuid().'.'.$extension;

        if (! Storage::disk($disk)->putFileAs(dirname($path), $file, basename($path), ['visibility' => $visibility])) {
            throw new RuntimeException('The file could not be stored.');
        }

        try {
            return DB::transaction(fn (): Media => Media::query()->create([
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize(),
                'visibility' => $visibility,
                'collection' => $collection,
                'uploaded_by' => $uploader?->getKey(),
                'metadata' => $metadata,
            ]));
        } catch (\Throwable $exception) {
            Storage::disk($disk)->delete($path);
            throw $exception;
        }
    }

    public function delete(Media $media): void
    {
        DB::transaction(function () use ($media): void {
            Storage::disk($media->disk)->delete($media->path);
            $media->delete();
        });
    }
}
