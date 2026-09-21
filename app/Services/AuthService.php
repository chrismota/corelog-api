<?php

namespace App\Services;

use App\DTOs\Auth\LoginDTO;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(LoginDTO $dto): ?array
    {
        $token = Auth::attempt([
            'email' => $dto->email,
            'password' => $dto->password,
        ]);

        if (!$token) {
            return null;
        }

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
        ];
    }

    public function me()
    {
        return Auth::user()->load('organization');
    }

    public function refresh(): array
    {
        return [
            'access_token' => Auth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
        ];
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
