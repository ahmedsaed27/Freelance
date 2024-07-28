<?php

namespace App\Rules;

use App\Models\Cases;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OwnerTheCase implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    private $case;

    public function __construct($case)
    {
        $this->case = $case;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $case = Cases::find($this->case);

        if (!$case || $case->user_id != auth()->guard('api')->id()) {
            $fail('Only Owner The Case Can Create Worked Case.');
        }
    }
}
