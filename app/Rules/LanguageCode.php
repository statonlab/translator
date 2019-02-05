<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LanguageCode implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return ! ! preg_match('/^[a-z]{2}-[A-Z]{2}$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid :attribute';
    }
}
