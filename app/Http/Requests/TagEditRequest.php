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
            'name' => ['required'],
            'color' => ['required', 'size:7', 'starts_with:#'],
            'archive' => ['sometimes', 'required', 'boolean']
        ];
    }
}
