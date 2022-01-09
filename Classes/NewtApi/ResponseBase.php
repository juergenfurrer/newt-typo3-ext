<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class ResponseBase
{
    protected bool $success = false;

    protected string $error = '';

    protected int $code = 200;


    /**
     * Get the value of success
     *
     * @return bool
     */
    public function getSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Set the value of success
     *
     * @param bool $success
     *
     * @return self
     */
    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    /**
     * Get the value of error
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Set the value of error
     *
     * @param string $error
     *
     * @return self
     */
    public function setError(int $code, string $error): self
    {
        $this->code = $code;
        $this->error = $error;
        $this->success = false;
        return $this;
    }

    /**
     * Get the value of code
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }


    /**
     * JsonSerializer
     *
     * @return mixed
     */
    public function getJson()
    {
        $data = [];
        $data["success"] = $this->success;
        if ($this->code > 0) {
            $data["code"] = $this->code;
        }
        if (!empty($this->error)) {
            $data["error"] = $this->error;
        }
        return (object)$data;
    }
}
