<?php

namespace App\Rules;

use App\Models\Cases;
use App\Models\CasesProfile;
use App\Models\Profiles;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateWorkedCaseWhenTheProfileIsAssigned implements ValidationRule
{
    private $case;
    private $profile;

    public function __construct($case , $profile)
    {
        $this->case = $case;
        $this->profile = $profile;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $case = Cases::find($this->case);

        // dd($case);
        $profile = Profiles::find($this->profile);

        if(!$case){
            $fail('case not found.');
            return;
        }

        if(!$profile){
            $fail('user dosnt have profile.');
            return;
        }

        if ($case->status != 'Assigned') {
            $fail('the case must be assigned.');
            return;
        }

        $caseProfile = CasesProfile::where('case_id' , $case->id)->where('profile_id' , $profile->id)
        ->first();

        if(!$caseProfile){
            $fail('the profile dosnt receive the case.');
            return;
        }


        if($caseProfile->status != 'Accepted'){
            $fail('The receive status must be accepted.');
            return;
        }

    }
}
