<?php

namespace RRZU\Validation\Validators;

use RRZU\Validation\Validation;

interface ValidatorInterface
{
    /**
     * 调用某个方法前执行验证器
     *
     * @param string $when 待调用的方法
     * @param array $data 待验证的数据
     * @param bool $throwError 是否抛出错误
     * @return mixed
     */
    public function validate(string $when, array $data, bool $throwError = true): ?ValidatorInterface;

    /**
     * 获取验证器
     *
     * @return Validation
     */
    public function getValidator(): Validation;

    /**
     * 获取已验证的数据
     *
     * @return array
     */
    public function getValidatedData(): array;

    /**
     * 获取有效数据
     *
     * @return array
     */
    public function getValidData(): array;

    /**
     * 获取无效数据
     *
     * @return array
     */
    public function getInvalidData(): array;
}
