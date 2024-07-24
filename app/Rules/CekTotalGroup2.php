<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CekTotalGroup2 implements Rule
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
        $group_2 = request()->input('group_2');
        return $value == $group_2;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Total grade group 2 harus sama dengan potongan dari group 2';
    }
}
