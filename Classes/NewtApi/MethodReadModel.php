<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class MethodReadModel
{
    protected int $backendUserUid = 0;

    protected int $pageUid = 0;

    protected int $readId = 0;


    /**
     * Get the value of backendUserUid
     *
     * @return int
     */
    public function getBackendUserUid(): int
    {
        return $this->backendUserUid;
    }

    /**
     * Set the value of backendUserUid
     *
     * @param int $backendUserUid
     *
     * @return self
     */
    public function setBackendUserUid(int $backendUserUid): self
    {
        $this->backendUserUid = $backendUserUid;
        return $this;
    }

    /**
     * Get the value of pageUid
     *
     * @return int
     */
    public function getPageUid(): int
    {
        return $this->pageUid;
    }

    /**
     * Set the value of pageUid
     *
     * @param int $pageUid
     *
     * @return self
     */
    public function setPageUid(int $pageUid): self
    {
        $this->pageUid = $pageUid;
        return $this;
    }

    /**
     * Get the value of readId
     *
     * @return int
     */
    public function getReadId(): int
    {
        return $this->readId;
    }

    /**
     * Set the value of readId
     *
     * @param int $readId
     *
     * @return self
     */
    public function setReadId(int $readId): self
    {
        $this->readId = $readId;

        return $this;
    }
}
