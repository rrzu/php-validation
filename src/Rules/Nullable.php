<?php

namespace RRZU\Validation\Rules;

use RRZU\Validation\Rule;

class Nullable extends Rule
{
    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return true;
    }
}
