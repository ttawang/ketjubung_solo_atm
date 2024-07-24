<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CekTotalGroup3 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $group_3 = request()->input('group_3');
        return $value == $group_3;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Total grade group 3 harus sama dengan potongan dari group 3';
    }
}
