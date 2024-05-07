<?php

namespace App\Http\Requests\V1;

use App\Rules\MatchOldPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:60',
            'current_password' => [
                'nullable',
                'min:8',
                'max:30',
                'required_with:password',
                new MatchOldPasswordRule(auth()->user()->password)
            ],
            'password' => [
                'nullable',
                'required_with:current_password',
                'different:current_password',
                'confirmed'
            ],
        ];
    }
}
