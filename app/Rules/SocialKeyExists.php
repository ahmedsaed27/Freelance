<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class SocialKeyExists implements ValidationRule
{
    private $keys = [];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $key = $value['social_id'];

        // Check if the key is unique within the request
        if (in_array($key, $this->keys)) {
            $fail("The {$attribute} key is duplicated.");
            return;
        }

    }
}
