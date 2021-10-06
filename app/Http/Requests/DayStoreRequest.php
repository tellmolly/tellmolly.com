<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DayStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d|unique:days,date',
            'comment' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'tag_ids.*' => 'nullable|exists:tags,id'
        ];
    }
}
