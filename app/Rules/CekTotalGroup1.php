<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CekTotalGroup1 implements Rule
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
        $group_1 = request()->input('group_1');
        return $value == $group_1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Total grade group 1 harus sama dengan potongan dari group 1';
    }
}
