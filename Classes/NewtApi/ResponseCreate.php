<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class ResponseCreate extends ResponseBase
{
    protected string $createdId = '';


    /**
     * Get the value of createdId
     *
     * @return string
     */
    public function getCreatedId(): string
    {
        return $this->createdId;
    }

    /**
     * Set the value of createdId
     *
     * @param string $createdId
     *
     * @return self
     */
    public function setCreatedId(string $createdId): self
    {
        $this->createdId = $createdId;
        return $this;
    }
}
