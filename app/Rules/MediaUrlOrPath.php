<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class MediaUrlOrPath implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && preg_match('~^/(?!/)[^\s\\\\]+$~D', $value)) {
            return;
        }

        if (Validator::make(['url' => $value], ['url' => 'required|url:http,https'])->fails()) {
            $fail('The :attribute must be a full HTTP(S) URL or a local path starting with / (for example, /images/photo.jpg).');
        }
    }
}
