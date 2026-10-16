<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Http\Requests\Concerns\NormalizesDayText;
use App\Models\Day;
use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DayStoreRequest extends FormRequest
{
    use NormalizesDayText;

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
            'date' => [
                'required',
                'date_format:Y-m-d',
                Rule::unique('days', 'date')
                    ->where(fn($query) => $query->where('user_id', $this->user()->id))
            ],
            'comment' => ['nullable', 'string', 'max:' . Day::COMMENT_MAX],
            'grateful_for' => ['nullable', 'string', 'max:' . Day::GRATEFUL_FOR_MAX],
            'category_id' => [
                'required',
                Rule::exists(Category::class, 'id')
            ],
            'tag_ids' => ['nullable'],
            'tag_ids.*' => Rule::forEach(function ($value, $attribute) {
                return [
                    Rule::exists(Tag::class, 'slug')->where(fn($query) => $query->where('user_id', $this->user()->id)),
                ];
            }),
        ];
    }
}
