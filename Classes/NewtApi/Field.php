<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class Field
{
    private string $name = '';

    private string $label = '';

    private string $type = FieldType::UNKNOWN;

    private string $value = '';

    private int $count = 0;

    private ?FieldValidation $validation = null;

    private array $items = [];


    /**
     * Returns the data for this Object
     *
     * @return void
     */
    public function getData()
    {
        $data = [];
        $data["name"] = $this->getName();
        $data["label"] = $this->getLabel();
        $data["type"] = $this->getType();
        if (! empty($this->getValue())) {
            $data["value"] = $this->getValue();
        }
        if (! empty($this->getCount())) {
            $data["count"] = $this->getCount();
        }
        if ($this->getValidation()) {
            $data["validation"] = $this->getValidation()->getData();
        }
        if (count($this->getItems()) > 0) {
            $data["items"] = $this->getItems();
        }

        return (object)$data;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
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

    /**
     * Get the value of type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
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

    /**
     * Get the value of count
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Set the value of count
     *
     * @param int $count
     *
     * @return self
     */
    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get the value of validation
     *
     * @return ?FieldValidation
     */
    public function getValidation(): ?FieldValidation
    {
        return $this->validation;
    }

    /**
     * Set the value of validation
     *
     * @param ?FieldValidation $validation
     *
     * @return self
     */
    public function setValidation(?FieldValidation $validation): self
    {
        $this->validation = $validation;
        return $this;
    }

    /**
     * Get the value of items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @param array $items
     *
     * @return self
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Set the value of items
     *
     * @param FieldItem $items
     *
     * @return self
     */
    public function addItem(FieldItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }
}
