<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login(
            LoginDTO::fromRequest($request)
        );

        if (!$result) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        return new AuthResource($result);
    }

    public function me()
    {
        return response()->json(
            Auth::user()->load('organization')
        );
    }

    public function refresh()
    {
        return new AuthResource($this->authService->refresh());
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }
}
