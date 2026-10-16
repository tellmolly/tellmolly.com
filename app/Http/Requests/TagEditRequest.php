<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'archive' => ['sometimes', 'required', 'boolean']
        ];
    }
}
