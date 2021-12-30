<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class Method
{
    protected string $type = '';

    protected string $url = '';


    /**
     * Returns the data for this Object
     *
     * @return object
     */
    public function getData()
    {
        return (object)[
            "type" => $this->getType(),
            "url" => $this->getUrl(),
        ];
    }


    /**
     * Get the value of type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the value of url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @param string $url
     *
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }
}
