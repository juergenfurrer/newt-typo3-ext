<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class EndpointOptions
{
    protected string $option1 = '';

    protected string $option2 = '';

    protected string $option3 = '';

    /**
     * Get the value of option1
     */ 
    public function getOption1()
    {
        return $this->option1;
    }

    /**
     * Set the value of option1
     *
     * @return  self
     */ 
    public function setOption1($option1)
    {
        $this->option1 = $option1;

        return $this;
    }

    /**
     * Get the value of option2
     */ 
    public function getOption2()
    {
        return $this->option2;
    }

    /**
     * Set the value of option2
     *
     * @return  self
     */ 
    public function setOption2($option2)
    {
        $this->option2 = $option2;

        return $this;
    }

    /**
     * Get the value of option3
     */ 
    public function getOption3()
    {
        return $this->option3;
    }

    /**
     * Set the value of option3
     *
     * @return  self
     */ 
    public function setOption3($option3)
    {
        $this->option3 = $option3;

        return $this;
    }
}
