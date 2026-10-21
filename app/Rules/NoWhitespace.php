<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Passwords may not contain spaces (or any other whitespace) - part of
 * Password::defaults(), so login, reset and the Users Add/Edit forms
 * all apply it.
 */
class NoWhitespace implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && preg_match('/\s/u', $value)) {
            $fail('The :attribute must not contain spaces.');
        }
    }
}
