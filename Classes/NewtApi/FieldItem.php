<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class FieldItem
{
    private string $value = '';

    private string $label = '';


    public function __construct($value = '', $label = '')
    {
        $this->value = strval($value);
        $this->label = strval($label);
    }

    /**
     * Get the value of value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @param string $value
     *
     * @return self
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the value of label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @param string $label
     *
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }
}
