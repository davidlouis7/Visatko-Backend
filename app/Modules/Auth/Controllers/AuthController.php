<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Requests\ChangePasswordRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\UpdateProfileRequest;
use App\Modules\Auth\Resources\UserResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check((string) $request->string('password'), $user->password)) {
            return $this->error('The provided credentials are incorrect.', 422, [
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            return $this->error('This account is inactive.', 403);
        }

        $user->forceFill(['last_login_at' => now()])->save();
        $token = $user->createToken($request->string('device_name')->value() ?: 'api-client');

        activity('auth')
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip(), 'user_agent' => $request->userAgent()])
            ->log('User logged in');

        return $this->success([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user),
        ], 'Login successful.');
    }

    public function logout(): JsonResponse
    {
        $user = request()->user();
        $user->currentAccessToken()?->delete();
        activity('auth')->causedBy($user)->log('User logged out');

        return $this->success(null, 'Logout successful.');
    }

    public function profile(): JsonResponse
    {
        return $this->success(UserResource::make(request()->user()));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return $this->success(UserResource::make($request->user()->refresh()), 'Profile updated successfully.');
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        if (! Hash::check((string) $request->string('current_password'), $request->user()->password)) {
            return $this->error('The current password is incorrect.', 422, [
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $request->user()->update(['password' => (string) $request->string('password')]);
        $currentTokenId = $request->user()->currentAccessToken()?->getKey();
        $request->user()->tokens()->when($currentTokenId, fn ($query) => $query->whereKeyNot($currentTokenId))->delete();

        activity('auth')->causedBy($request->user())->log('Password changed');

        return $this->success(null, 'Password changed successfully.');
    }
}
