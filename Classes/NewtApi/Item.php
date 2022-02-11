<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class Item
{
    protected string $id = '';

    protected string $title = '';

    protected string $description = '';

    protected string $label = '';

    protected string $labelColor = LabelColor::UNKNOWN;


    /**
     * @var array<ItemValue>
     */
    protected array $values = [];


    /**
     * Get the value of id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param string $id
     *
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of values
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Set the value of values
     *
     * @param array $values
     *
     * @return self
     */
    public function setValues(array $values): self
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Add a value to values
     *
     * @param ItemValue $value
     *
     * @return self
     */
    public function addValue(ItemValue $value): self
    {
        $this->values[] = $value;
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
     * Get the value of labelColor
     *
     * @return string
     */
    public function getLabelColor(): string
    {
        return $this->labelColor;
    }

    /**
     * Set the value of labelColor
     *
     * @param string $labelColor
     *
     * @return self
     */
    public function setLabelColor(string $labelColor): self
    {
        $this->labelColor = $labelColor;

        return $this;
    }
}
