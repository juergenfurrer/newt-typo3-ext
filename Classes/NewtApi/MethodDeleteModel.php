<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class MethodDeleteModel
{
    protected int $backendUserUid = 0;

    protected string $deleteId = '';


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
     * Get the value of deleteId
     *
     * @return string
     */
    public function getDeleteId(): string
    {
        return $this->deleteId;
    }

    /**
     * Set the value of deleteId
     *
     * @param string $deleteId
     *
     * @return self
     */
    public function setDeleteId(string $deleteId): self
    {
        $this->deleteId = $deleteId;

        return $this;
    }
}
