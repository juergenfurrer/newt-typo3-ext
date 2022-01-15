<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class MethodListModel
{
    protected int $backendUserUid = 0;

    protected int $pageUid = 0;

    protected string $lastKnownItemId = '';

    protected int $pageSize = 25;


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
     * Get the value of lastKnownItemId
     *
     * @return string
     */
    public function getLastKnownItemId(): string
    {
        return $this->lastKnownItemId;
    }

    /**
     * Set the value of lastKnownItemId
     *
     * @param string $lastKnownItemId
     *
     * @return self
     */
    public function setLastKnownItemId(string $lastKnownItemId): self
    {
        $this->lastKnownItemId = $lastKnownItemId;
        return $this;
    }

    /**
     * Get the value of pageSize
     *
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * Set the value of pageSize
     *
     * @param int $pageSize
     *
     * @return self
     */
    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }
}
