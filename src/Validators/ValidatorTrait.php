<?php

namespace RRZU\Validation\Validators;

use RRZU\Validation\Validation;
use RRZU\Validation\ValidationException;
use RRZU\Validation\Validator;

trait ValidatorTrait
{
    public $language;

    /**
     * @var array
     */
    private $validationData;

    /**
     * @var Validation
     */
    private $validator;

    abstract public function messages(): array;

    abstract public function attributes(): array;

    private function getMessage($rules): array
    {
        return array_merge($this->messages(), $rules['messages'] ?? []);
    }

    private function getAttributes($rules): array
    {
        return array_merge($this->attributes(), $rules['attributes'] ?? []);
    }

    private final function getRules(string $when): array
    {
        // 验证规则方法名
        $rulesMethod = 'rulesWhen' . ucfirst($when);

        // 返回验证规则
        return $this->{$rulesMethod}();
    }

    private final function validator(string $when, array $data): Validation
    {
        // 获取验证规则
        $rules = $this->getRules($when);

        $validator = new Validator($this->language);
        $validator = $validator->make($data, $rules['rules'], $this->getMessage($rules));
        $validator->setAliases($this->getAttributes($rules));
        return $validator;
    }

    private function throwError($throwError)
    {
        if ($throwError && $this->validator->fails()) {
            throw new ValidationException($this->validator->firstErrorMessage());
        }
    }
}
