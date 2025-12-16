<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ResponseTrait;

    public function __construct(protected AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $res_register = $this->authService->register($data);

        return $this->handleServiceResponse($res_register, 'success', 201, 500);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $result = $this->authService->login($data);

        return $this->handleServiceResponse($result, 'Login successful');
    }

    public function logout(Request $request)
    {
        $user = $request->user(); // Authenticated user
        
        $result = $this->authService->logout($user);
        
        return $this->handleServiceResponse($result, 'Logged out successfully');
    }
}
