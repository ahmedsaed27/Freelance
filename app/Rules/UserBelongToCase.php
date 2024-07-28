<?php

namespace App\Rules;

use App\Models\WorkedCases;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserBelongToCase implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    private $workedCaseID;
    private $user;

    public function __construct($workedCaseID , $user)
    {
        $this->workedCaseID = $workedCaseID;
        $this->user = $user;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $workedCase = WorkedCases::find($this->workedCaseID);
        
        if (!$workedCase || $workedCase->case?->user_id != $this->user || $workedCase->profile?->user_id != $this->user) {
            $fail('The user does not belong to the case.');
        }
    }
}
