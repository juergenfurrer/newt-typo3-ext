<?php

declare(strict_types=1);

namespace Swisscode\Newt\NewtApi;

interface EndpointOptionsInterface
{
    /**
     * Pass one EndpointOption to the class
     *
     * @param string $optionName
     * @param string $optionValue
     * @return void
     */
    public function addEndpointOption(string $optionName, string $optionValue): void;

    /**
     * Returns the array with needed options as an assoc-array ["key" => "label"]
     *
     * @return array
     */
    public function getNeededOptions(): array;

    /**
     * Returns the hint for EndpointOptions
     *
     * @return string|null
     */
    public function getEndpointOptionsHint(): ?string;
}
