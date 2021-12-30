<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class FieldValidation
{
    private bool $required = false;

    private int $minLength = 0;

    private string $pattern = '';


    /**
     * Returns the data for this Object
     *
     * @return void
     */
    public function getData()
    {
        $data = [];
        if ($this->getRequired()) {
            $data["required"] = true;
        }
        if ($this->getMinLength() > 0) {
            $data["minLength"] = $this->getMinLength();
        }
        if (! empty($this->getPattern())) {
            $data["pattern"] = $this->getPattern();
        }

        return (object)$data;
    }


    /**
     * Get the value of required
     *
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * Set the value of required
     *
     * @param bool $required
     *
     * @return self
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Get the value of minLength
     *
     * @return int
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * Set the value of minLength
     *
     * @param int $minLength
     *
     * @return self
     */
    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * Get the value of pattern
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Set the value of pattern
     *
     * @param string $pattern
     *
     * @return self
     */
    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }
}
