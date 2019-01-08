<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DayEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date_format:Y-m-d|unique:days,date,' . $this->day->id . ',id',
            'comment' => 'nullable',
            'category_id' => 'required|exists:categories,id'
        ];
    }
}
