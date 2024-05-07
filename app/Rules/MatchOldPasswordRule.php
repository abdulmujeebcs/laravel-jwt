<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MatchOldPasswordRule implements Rule
{
    protected $password;
    /**
     * Create a new rule instance.
     *
     * @ $password
     */
    public function __construct($password)
    {
        $this->password = $password;
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
        return \Hash::check($value, $this->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is invalid.';
    }
}
