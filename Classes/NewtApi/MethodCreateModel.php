<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class MethodCreateModel
{
    protected int $backendUserUid = 0;

    protected int $pageUid = 0;

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
     * Get the value of params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get the value of params
     *
     * @return string
     */
    public function getParam(string $key): string
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return "";
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
