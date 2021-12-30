<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class MethodUpdateModel
{
    protected int $backendUserUid = 0;

    protected int $uid = 0;

    protected array $params = [];



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
     * Get the value of uid
     *
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * Set the value of uid
     *
     * @param int $uid
     *
     * @return self
     */
    public function setUid(int $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Get the value of params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set the value of params
     *
     * @param array $params
     *
     * @return self
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }
}
