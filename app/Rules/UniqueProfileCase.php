<?php

namespace App\Rules;

use App\Models\WorkedCases;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueProfileCase implements ValidationRule
{
    protected $profileId;
    protected $caseId;

    public function __construct($profileId, $caseId)
    {
        $this->profileId = $profileId;
        $this->caseId = $caseId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = WorkedCases::query()
            ->where('profile_id', $this->profileId)
            ->where('case_id', $this->caseId)
            ->exists();

        if ($exists) {
            $fail('The combination of profile and case already exists.');
            return;
        }
    }
}
