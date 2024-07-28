<?php

namespace App\Rules;

use App\Models\CasesProfile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserBelongsToProfile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    private $caseProfileID;
    private $user;

    public function __construct($caseProfileID , $user)
    {
        $this->caseProfileID = $caseProfileID;
        $this->user = $user;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $caseProfile = CasesProfile::find($this->caseProfileID);

        if (!$caseProfile || $caseProfile->profile->user_id != $this->user) {
            $fail('The user does not belong to the profile.');
        }
    }
}
