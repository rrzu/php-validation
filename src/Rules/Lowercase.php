<?php

namespace RRZU\Validation\Rules;

use RRZU\Validation\Rule;

class Lowercase extends Rule
{

    /** @var string */
    protected $message = "The :attribute must be lowercase";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        return mb_strtolower($value, mb_detect_encoding($value)) === $value;
    }
}
