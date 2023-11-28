<?php
/**
 * 文件描述
 *
 * @author 傅增耀
 * @time 2023-09-28 12:13:27
 */

namespace RRZU\Validation\Rules;

use RRZU\Validation\Rule;

class StringHandler extends Rule
{

    /** @var string */
    protected $message = "The :attribute must be string";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return is_string($value);
    }
}
