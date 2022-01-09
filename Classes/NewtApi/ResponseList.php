<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class ResponseList extends ResponseBase
{
    protected array $items = [];

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
     * @param Item $item
     *
     * @return self
     */
    public function addItem(Item $item): self
    {
        $this->items[] = $item;
        return $this;
    }
}
