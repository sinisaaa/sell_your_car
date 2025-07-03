<?php

declare(strict_types=1);

namespace App\Helper\CustomQuery;


class WhereClauseFilter
{

    public const OPERAND_IS_EMPTY = 'isEmpty';
    public const OPERAND_EQUALS = '=';
    public const OPERAND_NOT_EQUAL = '<>';
    public const OPERAND_GREATER_THAN = '>';
    public const OPERAND_GREATER_EQUALS = '>=';
    public const OPERAND_LESS_THAN = '<';
    public const OPERAND_LESS_EQUALS = '<=';
    public const OPERAND_IN = 'in';
    public const OPERAND_NOT_IN = 'not in';
    public const OPERAND_LIKE = 'like';
    public const OPERAND_IS_NULL = 'isNull';
    public const OPERAND_IS_NOT_NULL = 'isNotNull';
    public const OPERAND_CUSTOM = 'custom';

    /**
     * EntityManagerWhereClauseFilter constructor.
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @param string|null $customParam
     */
    public function __construct(private string $field, private mixed $value, private string $operator, private ?string $customParam = null)
    {
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    /**
     * @return bool
     */
    public function shouldSetParam(): bool
    {
        return $this->getOperator() !== self::OPERAND_IS_EMPTY
            && $this->getOperator() !== self::OPERAND_IS_NULL
            && $this->getOperator() !== self::OPERAND_IS_NOT_NULL
            && $this->getOperator() !== self::OPERAND_LIKE
            && $this->getOperator() !== self::OPERAND_CUSTOM;
    }

    /**
     * @return string|null
     */
    public function getParam(): ?string
    {
        if ($this->customParam !== null) {
            return $this->customParam;
        }
        $field = $this->getField();
        $dotPos = strpos($field, '.');
        if ($dotPos) {
            $fieldArr = explode('.', $field);
            $field = $fieldArr[1];
        }
        return $field;
    }

}