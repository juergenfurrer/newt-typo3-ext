<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class Endpoint
{
    protected int $uid = 0;

    protected string $name = '';

    protected string $description = '';

    protected ?Configuration $configuration = null;


    /**
     * Returns the data for this Object
     *
     * @return object
     */
    public function getData()
    {
        $data = [];
        $data["uid"] = $this->getUid();
        $data["name"] = $this->getName();
        $data["description"] = $this->getDescription();
        if ($this->configuration) {
            $data["configuration"] = $this->configuration->getData();
        }

        return (object)$data;
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
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of configuration
     *
     * @return ?Configuration
     */
    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    /**
     * Set the value of configuration
     *
     * @param ?Configuration $configuration
     *
     * @return self
     */
    public function setConfiguration(?Configuration $configuration): self
    {
        $this->configuration = $configuration;
        return $this;
    }
}
