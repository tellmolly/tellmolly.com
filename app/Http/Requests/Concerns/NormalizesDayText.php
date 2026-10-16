<?php

namespace App\Http\Requests\Concerns;

trait NormalizesDayText
{
    /**
     * Browsers count a line break as one character against maxlength but
     * submit it as \r\n. Storing plain \n keeps the server's count the same
     * as the counter in the form.
     */
    protected function prepareForValidation(): void
    {
        foreach (['comment', 'grateful_for'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => str_replace("\r\n", "\n", $this->input($field))]);
            }
        }
    }
}
