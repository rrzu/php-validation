<?php

namespace RRZU\Validation\Rules;

use RRZU\Validation\Rule;

class Regex extends Rule
{

    /** @var string */
    protected $message = "The :attribute is not valid format";

    /** @var array */
    protected $fillableParams = ['regex'];

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }
        $this->requireParameters($this->fillableParams);
        $regex = $this->parameter('regex');
        return preg_match($regex, $value) > 0;
    }
}
