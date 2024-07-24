<?php

namespace App\Rules;

use App\Models\Cases;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSuggestedRate implements ValidationRule
{
    protected $caseId;

    public function __construct($caseId)
    {
        $this->caseId = $caseId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $case = Cases::find($this->caseId);

        if (!$case) {
            $fail('The selected case is invalid.');
            return;
        }

        if ($value < $case->min_amount || $value > $case->max_amount) {
            $fail('The suggested rate must be between ' . $case->min_amount . ' and ' . $case->max_amount . '.');
        }
    }
}
