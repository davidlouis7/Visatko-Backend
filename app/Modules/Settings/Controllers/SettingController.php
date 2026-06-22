<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Setting;
use App\Modules\Settings\Requests\StoreSettingRequest;
use App\Modules\Settings\Requests\UpdateSettingRequest;
use App\Modules\Settings\Resources\SettingResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    use ApiResponse;

    public function publicIndex(): JsonResponse
    {
        $settings = Setting::query()
            ->where('is_public', true)
            ->where('is_encrypted', false)
            ->orderBy('group')->orderBy('key')->get();

        $grouped = $settings->groupBy('group')->map(fn ($items) => $items->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->resolvedValue()]));

        return $this->success($grouped);
    }

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Setting::class);
        $settings = Setting::query()->orderBy('group')->orderBy('key')->get();

        return $this->success(SettingResource::collection($settings));
    }

    public function store(StoreSettingRequest $request): JsonResponse
    {
        $attributes = $request->safe()->except('value');
        $setting = new Setting($attributes);
        $setting->value = $setting->encodeValue($request->input('value'));
        $setting->save();

        activity('admin')->causedBy($request->user())->performedOn($setting)->log('Setting created');

        return $this->success(SettingResource::make($setting), 'Setting created successfully.', 201);
    }

    public function update(UpdateSettingRequest $request, Setting $setting): JsonResponse
    {
        $attributes = $request->safe()->except('value');
        $newEncrypted = (bool) ($attributes['is_encrypted'] ?? $setting->is_encrypted);
        $newPublic = (bool) ($attributes['is_public'] ?? $setting->is_public);

        if ($newEncrypted && $newPublic) {
            return $this->error('Encrypted settings cannot be public.', 422);
        }

        $oldValue = $setting->resolvedValue();
        $setting->fill($attributes);
        $setting->value = $setting->encodeValue($request->has('value') ? $request->input('value') : $oldValue);
        $setting->save();

        activity('admin')->causedBy($request->user())->performedOn($setting)->log('Setting updated');

        return $this->success(SettingResource::make($setting), 'Setting updated successfully.');
    }

    public function destroy(Setting $setting): JsonResponse
    {
        Gate::authorize('delete', $setting);
        $setting->delete();
        activity('admin')->causedBy(request()->user())->performedOn($setting)->log('Setting deleted');

        return $this->success(null, 'Setting deleted successfully.');
    }
}
