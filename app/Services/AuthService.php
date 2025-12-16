<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthService
{
    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        try {
            DB::beginTransaction();

            $user = User::create($data);
            $token = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            return [
                'code' => 1,
                'data' => [
                    'user'  => $user,
                    'token' => $token,
                ],
            ];
        } catch (Throwable $th) {
            DB::rollBack();
            return [
                'code' => 0,
                'msg'  => $th->getMessage(),
            ];
        }
    }


    /**
     * Login user and create token
     */
    public function login(array $data): array
    {
        try {
            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                return [
                    'code' => 0,
                    'msg'  => 'Invalid credentials',
                ];
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return [
                'code' => 1,
                'data' => [
                    'user'  => $user,
                    'token' => $token,
                ],
            ];
        } catch (Throwable $th) {
            return [
                'code' => 0,
                'msg'  => $th->getMessage(),
            ];
        }
    }


    /**
     * Logout authenticated user
     */
    public function logout($user): array
    {
        try {

            // Delete all tokens for this user (Sanctum)
            $user->currentAccessToken()->delete();

            return [
                'code' => 1,
                'data' => null,
            ];
        } catch (Throwable $th) {
            return [
                'code' => 0,
                'msg'  => $th->getMessage(),
            ];
        }
    }
}
