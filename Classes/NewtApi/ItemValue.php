<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class ItemValue
{
    protected string $key = '';

    protected string $value = '';


    /**
     * Constructor
     *
     * @param string $key
     * @param mixed $value
     */
    public function __construct($key = '', $value = null)
    {
        $this->key = $key;
        if ($value instanceof \DateTime) {
            $this->value = $value->format(\DateTime::ATOM);
        } else {
            $this->value = strval($value);
        }
    }


    /**
     * Get the value of key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set the value of key
     *
     * @param string $key
     *
     * @return self
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
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
}
