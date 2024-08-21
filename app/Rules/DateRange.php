<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class DateRange implements Rule
{
    protected $startDate;
    protected $endDate;

    /**
     * Create a new rule instance.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return void
     */
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->startDate);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->endDate);

        return $startDate->lt($endDate);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The start publication date must be before the end publication date.';
    }
}
