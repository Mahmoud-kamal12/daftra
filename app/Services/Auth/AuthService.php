<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return [
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken
        ];
    }

    /**
     * @throws ValidationException
     */
    public function login(array $data): array
    {
        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => ['Invalid credentials'],
            ]);
        }

        return [
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken
        ];
    }

    public function me(User $user): User
    {
        return $user;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
