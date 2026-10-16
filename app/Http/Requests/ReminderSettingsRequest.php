<?php

namespace App\Http\Requests;

use App\Services\TimezoneService;
use Illuminate\Foundation\Http\FormRequest;

class ReminderSettingsRequest extends FormRequest
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
            'reminder_enabled' => ['boolean'],
            'reminder_time' => ['required', 'date_format:H:i'],
            // "auto" follows the user's location; anything else pins a time zone.
            'timezone' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail) {
                if ($value !== 'auto' && ! (new TimezoneService)->isValid($value)) {
                    $fail('Please pick a time zone from the list.');
                }
            }],
        ];
    }
}
