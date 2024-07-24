<?php

namespace App\Rules;

use App\Models\Profiles;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCaseProfile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    protected $profileId;

    public function __construct($profileId)
    {
        $this->profileId = $profileId;
    }


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Profiles::whereHas('receive', function ($query) use ($value) {
            $query->where('case_id', $value)->where('profile_id', $this->profileId);
        })->exists();

        if ($exists) {
            $fail('The profile has already received this case.');
        }
    }
}
