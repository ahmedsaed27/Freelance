<?php

namespace App\Rules;

use App\Models\Cases;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WithinDays implements ValidationRule
{
    protected $case;

    public function __construct($case)
    {
        $this->case = $case;
    }


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $case = Cases::find($this->case);

        if(!$case){
            $fail('The selected case ID is invalid.');
            return;
        }
        // Convert value to a Carbon instance
        $estimationTime = Carbon::parse($value);
        $today = Carbon::today();

        // Calculate the difference in days
        $diffInDays = $estimationTime->diffInDays($today);

        // Check if the estimation time is within the allowed days
        if ($diffInDays > $case->number_of_days) {
            $fail("The {$attribute} must be within {$case->number_of_days} days from today.");
            return;
        }
    }
}
