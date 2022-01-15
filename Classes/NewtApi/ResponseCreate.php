<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class ResponseCreate extends ResponseBase
{
    protected Item $item;


    /**
     * Get the value of item
     *
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * Set the value of item
     *
     * @param Item $item
     *
     * @return self
     */
    public function setItem(Item $item): self
    {
        $this->item = $item;
        return $this;
    }

}
