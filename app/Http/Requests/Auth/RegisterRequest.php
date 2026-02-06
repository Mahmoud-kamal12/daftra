<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;

class RegisterRequest extends ApiFormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required','string','min:2','max:120'],
            'email' => ['required','email','max:190','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ];
    }
}
