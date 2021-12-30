<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class Configuration
{
    protected array $methods = [];

    protected array $fields = [];


    /**
     * Returns the data for this Object
     *
     * @return object
     */
    public function getData()
    {
        $methods = [];
        /** @var Method $method */
        foreach ($this->getMethods() as $method) {
            $methods[] = $method->getData();
        }

        $fields = [];
        /** @var Field $method */
        foreach ($this->getFields() as $field) {
            $fields[] = $field->getData();
        }

        $data = [];
        if (count($methods) > 0) {
            $data["methods"] = $methods;
        }
        if (count($fields) > 0) {
            $data["fields"] = $fields;
        }

        return (object)$data;
    }


    /**
     * Get the value of methods
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Set the value of methods
     *
     * @param array $methods
     *
     * @return self
     */
    public function setMethods(array $methods): self
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * Add new method
     *
     * @param Method $method
     *
     * @return self
     */
    public function addMethod(Method $method): self
    {
        $this->methods[] = $method;
        return $this;
    }

    /**
     * Get the value of fields
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Set the value of fields
     *
     * @param array $fields
     *
     * @return self
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Add new field
     *
     * @param array $field
     *
     * @return self
     */
    public function addField(Field $field): self
    {
        $this->fields[] = $field;
        return $this;
    }
}
